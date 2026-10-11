<?php
/**
 * CakePHP DatabaseLog Plugin
 *
 * Licensed under The MIT License.
 *
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 * @link https://github.com/dereuromark/CakePHP-DatabaseLog
 */

namespace DatabaseLog\Test\TestCase\Model\Table;

use Cake\Cache\Cache;
use Cake\Core\Configure;
use Cake\TestSuite\TestCase;
use Closure;
use DatabaseLog\Model\Table\DatabaseLogsTable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

#[CoversClass(DatabaseLogsTable::class)]
class DatabaseLogsTableTest extends TestCase {

	/**
	 * @var \DatabaseLog\Model\Table\DatabaseLogsTable
	 */
	public $Logs;

	/**
	 * @var array<string>
	 */
	protected array $fixtures = [
		'plugin.DatabaseLog.DatabaseLogs',
	];

	/**
	 * @var array<string, mixed>
	 */
	protected array $configBackup = [];

	/**
	 * Setup
	 *
	 * @return void
	 */
	public function setUp(): void {
		parent::setUp();

		foreach (['debug', 'DatabaseLog.disableAutoTable'] as $key) {
			$this->configBackup[$key] = Configure::check($key) ? Configure::read($key) : null;
		}

		$this->Logs = $this->fetchTable('DatabaseLog.DatabaseLogs');
		$this->Logs->truncate();
	}

	/**
	 * @return void
	 */
	public function tearDown(): void {
		foreach ($this->configBackup as $key => $value) {
			if ($value === null) {
				Configure::delete($key);

				continue;
			}

			Configure::write($key, $value);
		}

		parent::tearDown();
	}

	/**
	 * @return void
	 */
	public function testSave() {
		$data = [
			'type' => 'Foo',
			'summary' => 'some text',
			'message' => 'some text',
		];
		$log = $this->Logs->newEntity($data);
		$res = $this->Logs->save($log);
		$this->assertNotFalse($res);

		$this->assertNotEmpty($log->hostname);
		$this->assertNotEmpty($log->uri);
		$this->assertNotEmpty($log->user_agent);
		$this->assertTrue($log->isCli());
	}

	/**
	 * @return void
	 */
	public function testSaveHugeText() {
		$string = $this->string(66000);
		$result = $this->Logs->log(LOG_INFO, $string, [$string]);
		$this->assertTrue($result);
	}

	/**
	 * @param int $length
	 * @return string
	 */
	protected function string(int $length): string {
		$string = '';
		for ($i = 0; $i < $length; $i++) {
			$string .= 'X';
		}

		return $string;
	}

	/**
	 * @return void
	 */
	public function testLog() {
		$message = str_repeat('some very long text', 100);
		$result = $this->Logs->log(LOG_ERR, $message);
		$this->assertTrue($result);

		/** @var \DatabaseLog\Model\Entity\DatabaseLog $log */
		$log = $this->Logs->find()->orderByDesc('id')->firstOrFail();

		$this->assertNotEmpty($log->message);
		$this->assertSame(255, mb_strlen((string)$log->summary));
	}

	/**
	 * Tests the textSearch method
	 *
	 * @return void
	 */
	public function testTextSearch() {
		$res = $this->Logs->textSearch('interesting');
		$this->assertSame(['MATCH (message) AGAINST (\'interesting\')'], $res);

		$res = $this->Logs->textSearch('type@foo');
		$this->assertSame(['DatabaseLogs.type' => 'foo'], $res);
	}

	/**
	 * Tests the getTypes method
	 *
	 * @return void
	 */
	public function testGetTypes() {
		Cache::delete('database_log_types');

		$this->Logs->deleteAll([]);

		$data = [
			'type' => 'foo',
			'summary' => 'some text',
			'message' => 'some text',
		];
		$log = $this->Logs->newEntity($data);
		$res = $this->Logs->save($log);
		$this->assertNotFalse($res);

		$data = [
			'type' => 'bar',
			'summary' => 'some text',
			'message' => 'some more text',
		];
		$log = $this->Logs->newEntity($data);
		$res = $this->Logs->save($log);
		$this->assertNotFalse($res);

		$res = $this->Logs->getTypes();
		$this->assertSame(['bar' => 'bar', 'foo' => 'foo'], $res);
	}

	/**
	 * Tests the removeDuplicates method
	 *
	 * @return void
	 */
	public function testRemoveDuplicates() {
		$data = [
			'type' => 'Foo',
			'summary' => 'some text',
			'message' => 'some text',
		];
		$log = $this->Logs->newEntity($data);
		$res = $this->Logs->save($log);
		$this->assertNotFalse($res);

		$data = [
			'type' => 'Bar',
			'summary' => 'some text',
			'message' => 'some more text',
		];
		$log = $this->Logs->newEntity($data);
		$res = $this->Logs->save($log);
		$this->assertNotFalse($res);

		$data['message'] .= ' extra';
		$log = $this->Logs->newEntity($data);
		$res = $this->Logs->save($log);
		$this->assertNotFalse($res);

		$resBefore = $this->Logs->find()->count();
		$this->Logs->removeDuplicates();

		$resAfter = $this->Logs->find()->count();
		$this->assertSame($resBefore - 1, $resAfter, 'Res after is ' . $resAfter . ' but expected ' . ($resBefore - 1));
	}

	/**
	 * Auto-creation of the logs table defaults to debug mode only, and the explicit
	 * `disableAutoTable` config wins in both directions.
	 *
	 * @param bool $debug
	 * @param bool|null $disableAutoTable
	 * @param bool $expected
	 * @return void
	 */
	#[DataProvider('autoTableProvider')]
	public function testIsAutoTableEnabled(bool $debug, ?bool $disableAutoTable, bool $expected) {
		Configure::write('debug', $debug);
		Configure::write('DatabaseLog.disableAutoTable', $disableAutoTable);

		$result = Closure::bind(
			fn () => $this->isAutoTableEnabled(),
			$this->Logs,
			DatabaseLogsTable::class,
		)();

		$this->assertSame($expected, $result);
	}

	/**
	 * @return array<string, array{bool, bool|null, bool}>
	 */
	public static function autoTableProvider(): array {
		return [
			'debug on, not configured' => [true, null, true],
			'debug off, not configured' => [false, null, false],
			'debug off, explicitly enabled' => [false, false, true],
			'debug on, explicitly disabled' => [true, true, false],
		];
	}

	public function testRemoveDuplicatesPreservesPreviouslyMergedCounts(): void {
		$this->Logs->deleteAll([]);
		foreach ([3, 5] as $count) {
			$this->Logs->saveOrFail($this->Logs->newEntity([
				'type' => 'error',
				'summary' => 'Repeated message',
				'message' => 'Repeated message',
				'count' => $count,
			]));
		}

		$this->assertSame(1, $this->Logs->removeDuplicates());
		$this->assertSame(1, $this->Logs->find()->count());
		$this->assertSame(8, $this->Logs->find()->firstOrFail()->count);
	}

	public function testRemoveDuplicatesCountsLegacyZeroCountRows(): void {
		$this->Logs->deleteAll([]);
		foreach (range(1, 2) as $index) {
			$this->Logs->saveOrFail($this->Logs->newEntity([
				'type' => 'error',
				'summary' => 'Legacy message',
				'message' => 'Legacy message',
				'count' => 0,
			]));
		}

		$this->assertSame(1, $this->Logs->removeDuplicates());
		$this->assertSame(2, $this->Logs->find()->firstOrFail()->count);
	}

	/**
	 * @return void
	 */
	public function testReleasedSubclassDeclarationsRemainSupported(): void {
		$table = new class (['connection' => $this->Logs->getConnection(), 'table' => 'database_logs', 'alias' => 'DatabaseLogs']) extends DatabaseLogsTable {
			/**
			 * @param bool $strict
			 * @return int
			 */
			public function removeDuplicates($strict = false) {
				return parent::removeDuplicates($strict);
			}

			/**
			 * @return int
			 */
			public function garbageCollector() {
				return parent::garbageCollector();
			}
		};
		$this->assertSame(0, $table->removeDuplicates());
		$this->assertSame(0, $table->removeDuplicates('1'));
		$this->assertSame(0, $table->garbageCollector());
	}

}
