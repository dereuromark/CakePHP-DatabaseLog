<?php

namespace DatabaseLog\Command;

use Cake\Console\ConsoleOptionParser;
use Cake\Log\Log;

class ResetCommand extends DatabaseLogCommand {

	/**
	 * @return string
	 */
	public static function getDescription(): string {
		return 'Truncate all log data.';
	}

	/**
	 * @return int|null|void The exit code or null for success
	 */
	public function execute() {
		$args = $this->args;
		$io = $this->io;

		if (!$args->getOption('quiet')) {
			$in = $io->askChoice('Sure?', ['y', 'n'], 'n');
			if ($in !== 'y') {
				$io->abort('Aborted!');
			}
		}

		$this->logs()->truncate();
		$io->success('Reset done');

		if ($args->getOption('test-entry')) {
			$level = (string)$args->getOption('test-entry');
			$context = [];
			if (str_contains($level, ':')) {
				[$level, $context] = explode(':', $level, 2);
			}
			Log::write($level, 'A test message from CLI' . ($context ? ' with scope `' . $context . '`' : ''), $context);
			$io->success('Test entry written with level `' . $level . '`' . ($context ? ' and scope `' . $context . '`' : ''));
		}
	}

	/**
	 * @return \Cake\Console\ConsoleOptionParser
	 */
	public function getOptionParser(): ConsoleOptionParser {
		$parser = parent::getOptionParser();
		$parser->addOptions([
			'limit' => [
				'help' => 'Limit (and optional offset, comma separated).',
				'short' => 'l',
			],
			'test-entry' => [
				'help' => 'With test entry. Select e.g. `' . implode('`, `', Log::levels()) . '` etc. You can also define a context using `{level}:{context}` definition.',
				'short' => 't',
			],
		])->setDescription('Resets the database, truncates all data. Use -q to skip confirmation.');

		return $parser;
	}

}
