<?php

namespace DatabaseLog\Shell;

use Cake\Console\ConsoleOptionParser;
use Cake\Console\Shell;
use Cake\Core\Configure;
use Cake\Log\Log;

/**
 * @property \DatabaseLog\Model\Table\DatabaseLogsTable $DatabaseLogs
 */
class DatabaseLogShell extends Shell {

	/**
		* @param string|null $type
		* @return void
		*/
	public function export($type = null) {
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
	 * @param array|string $context
	 *
	 * @return void
	 */
	public function testEntry($level = null, $message = null, $context = []) {
		$level = $level ?? LOG_INFO;
		$message = $message ?? 'test';

		Log::write($level, $message, $context);
	}

	/**
	 * @return \Cake\Console\ConsoleOptionParser
	 */
	public function getOptionParser(): ConsoleOptionParser {
		$parser = parent::getOptionParser();

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

		return $parser;
	}

}
