<?php

namespace DatabaseLog\Shell;

use Cake\Console\ConsoleOptionParser;
use Cake\Console\Shell;
use Cake\Log\Log;

/**
 * @property \DatabaseLog\Model\Table\DatabaseLogsTable $DatabaseLogs
 */
class DatabaseLogShell extends Shell {

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
