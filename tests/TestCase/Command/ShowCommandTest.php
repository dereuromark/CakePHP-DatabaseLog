<?php

namespace DatabaseLog\Test\TestCase\Command;

use Cake\Console\TestSuite\ConsoleIntegrationTestTrait;
use Cake\Log\Log;
use Cake\TestSuite\TestCase;

/**
 * @uses \DatabaseLog\Command\ShowCommand
 */
class ShowCommandTest extends TestCase {

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
		$this->exec('database_logs show -v');

		$output = $this->_out->output();
		$this->assertStringContainsString('DB type:', $output);
		$this->assertExitCode(0);
	}

	/**
	 * @return void
	 */
	public function testExecuteSimple(): void {
		Log::write('info', 'one');

		$this->exec('database_logs show');

		$output = $this->_out->output();
		$this->assertStringContainsString(': info - one', $output);
		$this->assertExitCode(0);
	}

}
