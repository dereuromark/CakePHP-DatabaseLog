<?php

namespace DatabaseLog\Shell;

use Cake\Console\Shell;
use Cake\Log\Log;
use Cake\Utility\Text;

/**
 * @author Mark Scherer
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 * @property \DatabaseLog\Model\Table\DatabaseLogsTable $DatabaseLogs
 */
class DatabaseLogShell extends Shell {

	/**
	 * @var string
	 */
	public $modelClass = 'DatabaseLog.DatabaseLogs';

	/**
	 * @return void
	 */
	public function cleanup() {
		$count = $this->DatabaseLogs->removeDuplicates();
		$this->info($count . ' duplicates removed (merging)');

		$count = $this->DatabaseLogs->garbageCollector();
		$this->info($count . ' outdated logs removed (garbage collector)');
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
		$query = $this->DatabaseLogs->find();
		if ($type) {
			$type = Text::tokenize($type);
			$query->where(['type IN' => $type]);
		}
		$limit = $this->param('limit');
		$offset = null;
		if (!$limit) {
			$limit = 20;
		} elseif (strpos($limit, ',') !== false) {
			$elements = explode(',', $limit);
			$offset = (int)$elements[0];
			$limit = (int)$elements[1];
		}

		/* @var \DatabaseLog\Model\Entity\DatabaseLog[] $logs */
		$logs = $query->order(['created' => 'DESC'])
			->limit($limit)
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

			$this->out($log->message, 1, Shell::VERBOSE);
		}
	}

	/**
	 * @param string|null $type
	 * @return void
	 */
	public function export($type = null) {
		if ($type) {
			$types = Text::tokenize($type);
		} else {
			$types = $this->DatabaseLogs->getTypes();
		}

		$limit = $this->param('limit') ?: 100;

		foreach ($types as $type) {
			$query = $this->DatabaseLogs->find();

			/* @var \DatabaseLog\Model\Entity\DatabaseLog[] $logs */
			$logs = $query->where(['type' => $type])
				->limit($limit)
				->order(['created' => 'DESC'])
				->all();
			$contentArray = [];
			foreach ($logs as $log) {
				$content = $log->created . ': ' . $log->type;
				if ($log->ip) {
					$content .= ' - IP: ' . $log->ip;
				}
				if ($log->refer) {
					$content .= ' - Referer: ' . $log->refer;
				}
				$content .= PHP_EOL . $log->message;

				$contentArray[] = $content;
			}

			$content = implode(PHP_EOL, $contentArray);
			file_put_contents(LOGS . 'export-' . $type . '.txt', $content);

			$this->out('Exporting type ' . $type . ': ' . count($logs) . ' entries written to export-' . $type . '.txt');
		}
	}

	/**
	 * @param string|null $level
	 * @param string|null $message
	 * @param string|null $scope
	 * @return void
	 */
	public function testEntry($level = null, $message = null, $scope = null) {
		$level = $level !== null ? $level : LOG_INFO;
		$message = $message !== null ? $message : 'test';

		Log::write($level, $message, $scope);
	}

	/**
	 * @return \Cake\Console\ConsoleOptionParser
	 */
	public function getOptionParser() {
		$parser = parent::getOptionParser();
		$parser->addSubcommand('show', [
			'help' => 'List log entries. Optionally per type only.',
			'parser' => [
				'options' => [
					'limit' => [
						'help' => 'Limit (and optional offset, comma separated).',
						'short' => 'l'
					],
				],
			]
		]);
		$parser->addSubcommand('export', [
			'help' => 'Export log entries. Optionally per type only.',
			'parser' => [
				'options' => [
					'limit' => [
						'help' => 'Limit.',
						'short' => 'l'
					],
				],
			]
		]);
		$parser->addSubcommand('cleanup', [
			'help' => 'Log rotation and other cleanup.',
		]);
		$parser->addSubcommand('reset', [
			'help' => 'Resets the database, truncates all data. Use -q to skip confirmation.',
		]);
		$parser->addSubcommand('test_entry', [
			'help' => 'Adds a test entry with a certain log type.',
			'parser' => [
				'arguments' => [
					'level' => [
						'help' => 'The log level to use ("' . implode('", "', Log::levels()) . '"), defaults to "info"',
						'required' => false
					],
					'message' => [
						'help' => 'The message, defaults to "test"',
						'required' => false
					],
					'context' => [
						'help' => 'The scope key, defaults to none',
						'required' => false
					]
				]
			]
		]);

		return $parser;
	}

}
