<?php

namespace DatabaseLog\Shell;

use Cake\Console\Shell;
use Cake\Log\Log;

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
		$count = $this->DatabaseLogs->garbageCollector();
		$this->info($count . ' removed');
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
		$parser->addSubcommand('cleanup', [
			'help' => 'Log rotation and other cleanup',
		]);
		$parser->addSubcommand('test_entry', [
			'help' => 'Add a test entry with a certain log type',
			'parser' => [
				'description' => [
					'xxx'
				],
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