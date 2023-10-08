<?php

namespace DatabaseLog\Command;

use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\Core\Configure;

class MonitorCommand extends Command {

	/**
	 * @var string|null
	 */
	protected ?string $defaultTable = 'DatabaseLog.DatabaseLogs';

	/**
	 * @param \Cake\Console\Arguments $args The command arguments.
	 * @param \Cake\Console\ConsoleIo $io The console io
	 * @return int|null|void The exit code or null for success
	 */
	public function execute(Arguments $args, ConsoleIo $io) {
		parent::execute($args, $io);

		$interval = Configure::read('DatabaseLog.notificationInterval');

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

		$query = $this->fetchTable('DatabaseLog.DatabaseLogs')->find()
			->where(['type IN' => $types]);
		if ($time) {
			$query->andWhere(['created >' => date('Y-m-d H:i:s', $time)]);
		}

		$logs = $query->orderBy(['created' => 'DESC'])
			->all();

		if (count($logs) < 1) {
			$io->out('All good...');

			return;
		}

		$this->fetchTable('DatabaseLog.DatabaseLogs')->notify($logs);
		file_put_contents(LOGS . 'export', time());

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
