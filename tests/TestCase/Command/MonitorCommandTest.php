<?php

namespace DatabaseLog\Test\TestCase\Command;

use Cake\Console\TestSuite\ConsoleIntegrationTestTrait;
use Cake\Core\Configure;
use Cake\Event\EventInterface;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use DatabaseLog\Command\MonitorCommand;
use PHPUnit\Framework\Attributes\UsesClass;

#[UsesClass(MonitorCommand::class)]
class MonitorCommandTest extends TestCase {

	use ConsoleIntegrationTestTrait;

	/**
	 * @return void
	 */
	public function setUp(): void {
		parent::setUp();

		if (!is_dir(LOGS)) {
			mkdir(LOGS, 0770, true);
		}

		if (file_exists(LOGS . 'export')) {
			unlink(LOGS . 'export');
		}

		Configure::write('DatabaseLog.notificationInterval', 60);

		$this->loadPlugins(['DatabaseLog']);
	}

	/**
	 * @return void
	 */
	public function testExecute(): void {
		$Logs = TableRegistry::getTableLocator()->get('DatabaseLog.DatabaseLogs');
		$Logs->truncate();

		Configure::write('DatabaseLog.monitor', ['error']);

		$this->exec('database_logs monitor');

		$output = $this->_out->output();
		$this->assertStringContainsString('All good...', $output);
		$this->assertExitCode(0);
	}

	/**
	 * @return void
	 */
	public function testExecuteInterval() {
		file_put_contents(LOGS . 'export', time() - 2);

		$this->exec('database_logs monitor');
		$output = $this->_out->output();

		$this->assertStringContainsString('Just ran... Will run again in 1 min', $output, $output);
	}

	public function testLogsWrittenDuringDeliveryAreReportedNextRun(): void {
		$logs = TableRegistry::getTableLocator()->get('DatabaseLog.DatabaseLogs');
		$logs->truncate();
		$logs->log('error', 'Initial alert');
		Configure::write('DatabaseLog.monitor', ['error']);
		Configure::write('DatabaseLog.notificationInterval', 1);
		$reports = [];
		Configure::write('DatabaseLog.monitorCallback', function (EventInterface $event) use (&$reports, $logs): void {
			$reports[] = array_map(static fn ($entry): string => $entry->message, $event->getData('logs'));
			if (count($reports) === 1) {
				usleep(1100000);
				$logs->log('error', 'Written during delivery');
			}
		});
		TableRegistry::getTableLocator()->remove('DatabaseLog.DatabaseLogs');

		$this->exec('database_logs monitor');
		$this->exec('database_logs monitor');

		$this->assertCount(2, $reports);
		$this->assertContains('Written during delivery', $reports[1]);
	}

}
