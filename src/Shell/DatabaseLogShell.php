<?php

namespace DatabaseLog\Shell;

use Cake\Console\ConsoleOptionParser;
use Cake\Console\Shell;
use Cake\Core\Configure;
use Cake\I18n\Number;
use Cake\Log\Log;
use Cake\Utility\Text;

/**
 * @property \DatabaseLog\Model\Table\DatabaseLogsTable $DatabaseLogs
 */
class DatabaseLogShell extends Shell {

	/**
	 * @var string
	 */
	protected $modelClass = 'DatabaseLog.DatabaseLogs';

	/**
	 * @return void
	 */
	public function cleanup() {
		$count = $this->DatabaseLogs->garbageCollector();
		$this->info($count . ' outdated logs removed (garbage collector)');

		$count = $this->DatabaseLogs->removeDuplicates();
		$this->info($count . ' duplicates removed (merging)');
	}

	/**
	 * @return void
	 */
	public function reset() {
		if (!$this->param('quiet')) {
			$in = $this->in('Sure?', ['y', 'n'], 'n');
			if ($in !== 'y') {
				$this->abort('Aborted!');
			}
		}

		$this->DatabaseLogs->truncate();
		$this->info('Reset done');
	}

	/**
	 * @param string|null $type
	 * @return void
	 */
	public function show($type = null) {
		if ($this->param('verbose')) {
			$dbType = $this->DatabaseLogs->databaseType();
			$out = 'DB type: ' . $dbType;
			$dbSize = $this->DatabaseLogs->databaseSize();
			if ($dbSize !== null) {
				$out .= ' (' . Number::toReadableSize($dbSize) . ')';
			}
			$this->out($out);
		}

		$query = $this->DatabaseLogs->find();
		if ($type) {
			$types = Text::tokenize($type);
			$query->where(['type IN' => $types]);
		}
		$limit = (string)$this->param('limit');
		$offset = null;
		if (!$limit) {
			$limit = 20;
		} elseif (strpos($limit, ',') !== false) {
			$elements = explode(',', $limit);
			$offset = (int)$elements[0];
			$limit = (int)$elements[1];
		}

		/** @var \DatabaseLog\Model\Entity\DatabaseLog[] $logs */
		$logs = $query->order(['created' => 'DESC'])
			->limit((int)$limit)
			->offset($offset)
			->all();

		foreach ($logs as $log) {
			$content = $log->created . ': ' . $log->type;
			$pieces = explode("\n", trim($log->message), 2);
			$shortMessage = Text::truncate(trim($pieces[0]), 100);
			$content .= ' - ' . $shortMessage;

			if ($log->type === 'error') {
				$this->err($content);
			} elseif ($log->type === 'warning' || $log->type === 'notice') {
				$this->warn($content);
			} else {
				$this->out($content);
			}

			$this->out($log->message, 2, Shell::VERBOSE);
		}
	}

	/**
	 * @param string|null $type
	 * @return void
	 */
	public function export($type = null) {
		if ($type) {
			$types = (array)Text::tokenize($type);
		} else {
			$types = $this->DatabaseLogs->getTypes();
		}

		$limit = (int)$this->param('limit') ?: 100;

		foreach ($types as $type) {
			$query = $this->DatabaseLogs->find();

			/** @var \DatabaseLog\Model\Entity\DatabaseLog[] $logs */
			$logs = $query->where(['type' => $type])
				->limit($limit)
				->order(['created' => 'DESC'])
				->all();
			$contentArray = [];
			foreach ($logs as $log) {
				$content = $this->DatabaseLogs->format($log) . PHP_EOL;

				$contentArray[] = $content;
			}

			$content = implode(PHP_EOL, $contentArray);
			file_put_contents(LOGS . 'export-' . $type . '.txt', $content);

			$this->out('Exporting type ' . $type . ': ' . count($logs) . ' entries written to export-' . $type . '.txt');
		}
	}

	/**
	 * @return void
	 */
	public function monitor() {
		$interval = Configure::read('DatabaseLog.notificationInterval');

		$time = null;
		if (file_exists(LOGS . 'export')) {
			$time = (int)file_get_contents(LOGS . 'export');
		}

		if ($time + $interval > time()) {
			$secondsLeft = $time + $interval - time();
			$this->out('Just ran... Will run again in ' . round($secondsLeft / 60) . ' min');

			return;
		}

		$types = (array)Configure::read('DatabaseLog.monitor');
		if (!$types) {
			$this->abort('No DatabaseLog.monitor types defined.');
		}

		$query = $this->DatabaseLogs->find()
			->where(['type IN' => $types]);
		if ($time) {
			$query->andWhere(['created >' => date('Y-m-d H:i:s', $time)]);
		}

		$logs = $query->order(['created' => 'DESC'])
			->all();

		if (count($logs) < 1) {
			$this->out('All good...');

			return;
		}

		$this->DatabaseLogs->notify($logs);
		file_put_contents(LOGS . 'export', time());

		$this->out(count($logs) . ' new log entries reported.');
	}

	/**
	 * @param string|int|null $level
	 * @param string|null $message
	 * @param string|array $context
	 *
	 * @return void
	 */
	public function testEntry($level = null, $message = null, $context = []) {
		$level = $level !== null ? $level : LOG_INFO;
		$message = $message !== null ? $message : 'test';

		Log::write($level, $message, $context);
	}

	/**
	 * @return \Cake\Console\ConsoleOptionParser
	 */
	public function getOptionParser(): ConsoleOptionParser {
		$parser = parent::getOptionParser();
		$parser->addSubcommand('show', [
			'help' => 'List log entries. Optionally per type only. Use -v for db related details.',
			'parser' => [
				'options' => [
					'limit' => [
						'help' => 'Limit (and optional offset, comma separated).',
						'short' => 'l',
					],
				],
			],
		]);
		$parser->addSubcommand('export', [
			'help' => 'Export log entries. Optionally per type only.',
			'parser' => [
				'options' => [
					'limit' => [
						'help' => 'Limit.',
						'short' => 'l',
					],
				],
			],
		]);
		$parser->addSubcommand('cleanup', [
			'help' => 'Log rotation and other cleanup.',
		]);
		$parser->addSubcommand('reset', [
			'help' => 'Resets the database, truncates all data. Use -q to skip confirmation.',
		]);
		$parser->addSubcommand('monitor', [
			'help' => 'Run as cronjob to monitor your logs',
		]);
		$parser->addSubcommand('test_entry', [
			'help' => 'Adds a test entry with a certain log type.',
			'parser' => [
				'arguments' => [
					'level' => [
						'help' => 'The log level to use ("' . implode('", "', Log::levels()) . '"), defaults to "info"',
						'required' => false,
					],
					'message' => [
						'help' => 'The message, defaults to "test"',
						'required' => false,
					],
					'context' => [
						'help' => 'The scope key, defaults to none',
						'required' => false,
					],
				],
			],
		]);

		return $parser;
	}

}
