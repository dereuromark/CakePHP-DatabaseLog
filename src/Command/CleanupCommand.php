<?php

namespace DatabaseLog\Command;

use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;

class CleanupCommand extends Command {

	/**
	 * @return string
	 */
	public static function getDescription(): string {
		return 'Log rotation and cleanup.';
	}

	/**
	 * @param \Cake\Console\Arguments $args The command arguments.
	 * @param \Cake\Console\ConsoleIo $io The console io
	 * @return int|null|void The exit code or null for success
	 */
	public function execute(Arguments $args, ConsoleIo $io) {
		parent::execute($args, $io);

		$count = $this->fetchTable('DatabaseLog.DatabaseLogs')->garbageCollector();
		$io->success($count . ' outdated logs removed (garbage collector)');

		$count = $this->fetchTable('DatabaseLog.DatabaseLogs')->removeDuplicates();
		$io->success($count . ' duplicates removed (merging)');
	}

	/**
	 * @return \Cake\Console\ConsoleOptionParser
	 */
	public function getOptionParser(): ConsoleOptionParser {
		$parser = parent::getOptionParser();
		$parser->setDescription('Log rotation and other cleanup.');

		return $parser;
	}

}
