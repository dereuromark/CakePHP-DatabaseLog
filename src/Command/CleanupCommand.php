<?php

namespace DatabaseLog\Command;

use Cake\Console\ConsoleOptionParser;

class CleanupCommand extends DatabaseLogCommand {

	/**
	 * @return string
	 */
	public static function getDescription(): string {
		return 'Log rotation and cleanup.';
	}

	/**
	 * @return int|null|void The exit code or null for success
	 */
	public function execute() {
		$io = $this->io;

		$count = $this->logs()->garbageCollector();
		$io->success($count . ' outdated logs removed (garbage collector)');

		$count = $this->logs()->removeDuplicates();
		$io->success($count . ' duplicate groups merged');
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
