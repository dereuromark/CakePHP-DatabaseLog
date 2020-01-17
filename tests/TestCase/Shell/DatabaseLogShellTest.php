<?php

namespace DatabaseLog\Test\TestCase\Shell;

use Cake\Console\ConsoleIo;
use Cake\Core\Configure;
use Cake\Log\Log;
use Cake\ORM\TableRegistry;
use DatabaseLog\Shell\DatabaseLogShell;
use Shim\TestSuite\ConsoleOutput;
use Shim\TestSuite\TestCase;

class DatabaseLogShellTest extends TestCase {

	/**
	 * @var array
	 */
	protected $fixtures = [
		'plugin.DatabaseLog.DatabaseLogs',
	];

	/**
	 * @var \DatabaseLog\Shell\DatabaseLogShell|\PHPUnit\Framework\MockObject\MockObject
	 */
	protected $Shell;

	/**
	 * @var \DatabaseLog\Model\Table\DatabaseLogsTable
	 */
	protected $Logs;

	/**
	 * @var \Shim\TestSuite\ConsoleOutput
	 */
	protected $out;

	/**
	 * @var \Shim\TestSuite\ConsoleOutput
	 */
	protected $err;

	/**
	 * @return void
	 */
	public function setUp(): void {
		parent::setUp();

		if (!is_dir(LOGS)) {
			mkdir(LOGS, 0770, true);
		}

		Configure::delete('DatabaseLog');
		Configure::write('DatabaseLog.monitor', ['info']);
		Configure::write('DatabaseLog.notificationInterval', MINUTE);

		$this->out = new ConsoleOutput();
		$this->err = new ConsoleOutput();
		$io = new ConsoleIo($this->out, $this->err);

		$this->Shell = $this->getMockBuilder(DatabaseLogShell::class)
			->setMethods(['in', '_stop'])
			->setConstructorArgs([$io])
			->getMock();

		$this->Logs = TableRegistry::get('DatabaseLog.DatabaseLogs');
		$this->Logs->truncate();
	}

	/**
	 * @return void
	 */
	public function tearDown(): void {
		parent::tearDown();
		unset($this->Shell);
	}

	/**
	 * @return void
	 */
	public function testShow() {
		Log::write('info', 'one');

		$this->Shell->runCommand(['show']);
		$output = (string)$this->out->output();

		$this->assertStringContainsString(': info - one', $output, $output);
	}

	/**
	 * @return void
	 */
	public function testExport() {
		if (!is_dir(LOGS)) {
			mkdir(LOGS, 0770, true);
		}

		Log::write('info', 'one');

		$this->Shell->runCommand(['export']);
		$output = (string)$this->out->output();

		$this->assertStringContainsString('entries written to export-info.txt', $output, $output);
	}

	/**
	 * @return void
	 */
	public function testMonitor() {
		if (file_exists(LOGS . 'export')) {
			unlink(LOGS . 'export');
		}

		Log::write('info', 'one');

		$this->Shell->runCommand(['monitor']);
		$output = (string)$this->out->output();

		$this->assertStringContainsString(' new log entries reported', $output, $output);
	}

	/**
	 * @return void
	 */
	public function testMonitorInterval() {
		file_put_contents(LOGS . 'export', time() - 2);

		$this->Shell->runCommand(['monitor']);
		$output = (string)$this->out->output();

		$this->assertStringContainsString('Just ran... Will run again in 1 min', $output, $output);
	}

	/**
	 * @return void
	 */
	public function testCleanup() {
		Log::write('info', 'one');
		Log::write('info', 'two');
		Log::write('info', 'three');

		$this->Logs->updateAll(['created' => date('Y-m-d H:i:s', time() - DAY)], '1 = 1');

		Log::write('info', 'four');
		Log::write('info', 'five');
		Log::write('info', 'six');

		$count = $this->Logs->find()->count();
		$this->assertTrue($count > 4);

		Configure::write('DatabaseLog.limit', 2);
		Configure::write('DatabaseLog.maxLength', '-1 hour');

		$this->Shell->runCommand(['cleanup']);
		$output = (string)$this->out->output();
		$this->assertNotEmpty($output);

		$this->assertStringContainsString('10 outdated logs removed', $output);
		$this->assertStringContainsString('1 duplicates removed', $output);

		$count = $this->Logs->find()->count();
		$this->assertSame(1, $count);
	}

	/**
	 * @return void
	 */
	public function testReset() {
		Log::write('info', 'six');

		$count = $this->Logs->find()->count();
		$this->assertTrue($count > 0);

		$this->Shell->runCommand(['reset', '-q']);

		$count = $this->Logs->find()->count();
		$this->assertSame(0, $count);
	}

	/**
	 * @return void
	 */
	public function testTestEntry() {
		$count = $this->Logs->find()->count();

		$this->Shell->runCommand(['test_entry']);
		//$output = $this->out->output();

		$newCount = $this->Logs->find()->count();

		$this->assertSame($count + 2, $newCount); // Should only be 1...
	}

	/**
	 * @return void
	 */
	public function testTestEntryCustom() {
		$this->Shell->runCommand(['test_entry', 'warning', 'My warning']);

		$log = $this->Logs->find()->order(['id' => 'DESC'])->first();

		$this->assertSame('warning', $log->type);
		$this->assertSame('My warning', $log->message);
	}

}
