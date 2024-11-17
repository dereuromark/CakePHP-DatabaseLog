<?php

namespace DatabaseLog\Command;

use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\Log\Log;

class ResetCommand extends Command {

	/**
	 * @param \Cake\Console\Arguments $args The command arguments.
	 * @param \Cake\Console\ConsoleIo $io The console io
	 * @return int|null|void The exit code or null for success
	 */
	public function execute(Arguments $args, ConsoleIo $io) {
		parent::execute($args, $io);

		if (!$args->getOption('quiet')) {
			$in = $io->askChoice('Sure?', ['y', 'n'], 'n');
			if ($in !== 'y') {
				$io->abort('Aborted!');
			}
		}

		$this->fetchTable('DatabaseLog.DatabaseLogs')->truncate();
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
