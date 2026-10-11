<?php

namespace DatabaseLog\Command;

use Cake\Console\ConsoleOptionParser;
use Cake\Core\Configure;
use RuntimeException;

class MonitorCommand extends DatabaseLogCommand {

	/**
	 * @return string
	 */
	public static function getDescription(): string {
		return 'Monitor logs via cronjob.';
	}

	/**
	 * @var string|null
	 */
	protected ?string $defaultTable = 'DatabaseLog.DatabaseLogs';

	/**
	 * @return int|null|void The exit code or null for success
	 */
	public function execute() {
		$io = $this->io;

		$interval = (int)Configure::read('DatabaseLog.notificationInterval');
		if (!$interval) {
			throw new RuntimeException('You need to define an interval as config `DatabaseLog.notificationInterval`');
		}

		$time = null;
		if (file_exists(LOGS . 'export')) {
			$time = (int)file_get_contents(LOGS . 'export');
		}

		if ($time + $interval > time()) {
			$secondsLeft = $time + $interval - time();
			$io->out('Just ran... Will run again in ' . round($secondsLeft / 60) . ' min');

			return;
		}

		$types = (array)Configure::read('DatabaseLog.monitor');
		if (!$types) {
			$io->abort('No DatabaseLog.monitor types defined.');
		}

		$checkedAt = time();
		$query = $this->logs()->find()
			->where(['type IN' => $types, 'created <' => date('Y-m-d H:i:s', $checkedAt + 1)]);
		if ($time) {
			$query->andWhere(['created >=' => date('Y-m-d H:i:s', $time)]);
		}

		$logs = $query->orderBy(['created' => 'DESC'])
			->all()->toArray();

		if (count($logs) < 1) {
			$io->out('All good...');

			return;
		}

		$this->logs()->notify($logs);
		file_put_contents(LOGS . 'export', $checkedAt);

		$io->out(count($logs) . ' new log entries reported.');
	}

	/**
	 * @return \Cake\Console\ConsoleOptionParser
	 */
	public function getOptionParser(): ConsoleOptionParser {
		$parser = parent::getOptionParser();
		$parser->setDescription('Run as cronjob to monitor your logs');

		return $parser;
	}

}
