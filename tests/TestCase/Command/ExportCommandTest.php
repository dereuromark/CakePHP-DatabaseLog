<?php

namespace DatabaseLog\Test\TestCase\Command;

use Cake\Console\TestSuite\ConsoleIntegrationTestTrait;
use Cake\Log\Log;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * @uses \DatabaseLog\Command\ShowCommand
 */
class ExportCommandTest extends TestCase {

	use ConsoleIntegrationTestTrait;

	/**
	 * @return void
	 */
	public function setUp(): void {
		parent::setUp();

		if (!is_dir(LOGS)) {
			mkdir(LOGS, 0770, true);
		}

		$this->loadPlugins(['DatabaseLog']);
	}

	/**
	 * @return void
	 */
	public function testExecute(): void {
		$Logs = TableRegistry::getTableLocator()->get('DatabaseLog.DatabaseLogs');
		$Logs->truncate();

		$this->exec('database_logs export');

		$output = $this->_out->output();
		$this->assertStringContainsString('Nothing to do...', $output);
		$this->assertExitCode(0);
	}
	/**
	 * @return void
	 */
	public function testExecuteSimple(): void {
		Log::write('info', 'one');

		$this->exec('database_logs export');

		$output = $this->_out->output();
		$this->assertStringContainsString('entries written to export-info.txt', $output);
		$this->assertExitCode(0);
	}

}
