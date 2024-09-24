<?php

namespace DatabaseLog\Test\TestCase\Shell;

use Cake\Console\ConsoleIo;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use DatabaseLog\Shell\DatabaseLogShell;
use Shim\TestSuite\ConsoleOutput;
use Shim\TestSuite\TestCase;

class DatabaseLogShellTest extends TestCase {

	/**
	 * @var array<string>
	 */
	protected array $fixtures = [
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

		$this->skipIf(true, 'Refactor to command tests');

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
			->onlyMethods(['in', '_stop'])
			->setConstructorArgs([$io])
			->getMock();

		$this->Logs = TableRegistry::getTableLocator()->get('DatabaseLog.DatabaseLogs');
		$this->Logs->truncate();
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
