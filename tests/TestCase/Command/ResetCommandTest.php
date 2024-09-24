<?php

namespace DatabaseLog\Test\TestCase\Command;

use Cake\Console\TestSuite\ConsoleIntegrationTestTrait;
use Cake\Log\Log;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * @uses \DatabaseLog\Command\MonitorCommand
 */
class ResetCommandTest extends TestCase {

	use ConsoleIntegrationTestTrait;

	/**
	 * @return void
	 */
	public function setUp(): void {
		parent::setUp();

		$this->loadPlugins(['DatabaseLog']);
	}

	/**
	 * @return void
	 */
	public function testExecute(): void {
		$this->exec('database_logs reset', ['y']);

		$output = $this->_out->output();
		$this->assertStringContainsString('Reset done', $output);
		$this->assertExitCode(0);
	}

	/**
	 * @return void
	 */
	public function testReset() {
		Log::write('info', 'six');

		$Logs = TableRegistry::getTableLocator()->get('DatabaseLog.DatabaseLogs');
		$count = $Logs->find()->count();
		$this->assertTrue($count > 0);

		$this->exec('database_logs reset -q');

		$count = $Logs->find()->count();
		$this->assertSame(0, $count);
	}

}
