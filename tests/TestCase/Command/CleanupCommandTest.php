<?php

namespace DatabaseLog\Test\TestCase\Command;

use Cake\Console\TestSuite\ConsoleIntegrationTestTrait;
use Cake\Core\Configure;
use Cake\Log\Log;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * @uses \DatabaseLog\Command\MonitorCommand
 */
class CleanupCommandTest extends TestCase {

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
		$this->exec('database_logs cleanup');

		$output = $this->_out->output();
		$this->assertStringContainsString('outdated logs removed', $output);
		$this->assertStringContainsString('duplicates removed', $output);
		$this->assertExitCode(0);
	}

	/**
	 * @return void
	 */
	public function testCleanup() {
		Log::write('info', 'one');
		Log::write('info', 'two');
		Log::write('info', 'three');

		$Logs = TableRegistry::getTableLocator()->get('DatabaseLog.DatabaseLogs');
		$Logs->updateAll(['created' => date('Y-m-d H:i:s', time() - DAY)], '1 = 1');

		Log::write('info', 'four');
		Log::write('info', 'five');
		Log::write('info', 'six');

		$count = $Logs->find()->count();
		$this->assertTrue($count > 4);

		Configure::write('DatabaseLog.limit', 2);
		Configure::write('DatabaseLog.maxLength', '-1 hour');

		$this->exec('database_logs cleanup');
		$output = $this->_out->output();
		$this->assertNotEmpty($output);

		$this->assertStringContainsString('10 outdated logs removed', $output);
		$this->assertStringContainsString('1 duplicates removed', $output);

		$count = $Logs->find()->count();
		$this->assertSame(1, $count);
	}

}
