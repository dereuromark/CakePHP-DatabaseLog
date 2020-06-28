<?php
/**
 * CakePHP DatabaseLog Plugin
 *
 * Licensed under The MIT License.
 *
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 * @link https://github.com/dereuromark/CakePHP-DatabaseLog
 */

namespace DatabaseLog\TestCase\Model\Table;

use Cake\Cache\Cache;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * @coversDefaultClass \DatabaseLog\Model\Table\DatabaseLogsTable
 */
class DatabaseLogsTableTest extends TestCase {

	/**
	 * @var \DatabaseLog\Model\Table\DatabaseLogsTable
	 */
	public $Logs;

	/**
	 * @var array
	 */
	protected $fixtures = [
		'plugin.DatabaseLog.DatabaseLogs',
	];

	/**
	 * Setup
	 *
	 * @return void
	 */
	public function setUp(): void {
		$this->Logs = TableRegistry::get('DatabaseLog.DatabaseLogs');
		$this->Logs->truncate();

		parent::setUp();
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
		$this->assertTrue(!empty($res));

		$this->assertNotEmpty($log->hostname);
		$this->assertNotEmpty($log->uri);
		$this->assertNotEmpty($log->user_agent);
		$this->assertTrue($log->isCli());
	}

	/**
	 * @return void
	 */
	public function testLog() {
		$message = str_repeat('some very long text', 100);
		$result = $this->Logs->log(LOG_ERR, $message);
		$this->assertTrue($result);

		/** @var \DatabaseLog\Model\Entity\DatabaseLog $log */
		$log = $this->Logs->find()->orderDesc('id')->firstOrFail();

		$this->assertNotEmpty($log->message);
		$this->assertSame(255, mb_strlen($log->summary));
	}

	/**
	 * Tests the textSearch method
	 *
	 * @covers ::textSearch
	 * @return void
	 */
	public function testTextSearch() {
		$res = $this->Logs->textSearch('interesting');
		$this->assertEquals(['MATCH (message) AGAINST (\'interesting\')'], $res);

		$res = $this->Logs->textSearch('type@foo');
		$this->assertEquals(['Log.type' => 'foo'], $res);
	}

	/**
	 * Tests the getTypes method
	 *
	 * @covers ::getTypes
	 * @return void
	 */
	public function testGetTypes() {
		Cache::delete('database_log_types');

		$this->Logs->deleteAll('1=1');

		$data = [
			'type' => 'foo',
			'summary' => 'some text',
			'message' => 'some text',
		];
		$log = $this->Logs->newEntity($data);
		$res = $this->Logs->save($log);
		$this->assertTrue(!empty($res));

		$data = [
			'type' => 'bar',
			'summary' => 'some text',
			'message' => 'some more text',
		];
		$log = $this->Logs->newEntity($data);
		$res = $this->Logs->save($log);
		$this->assertTrue((bool)$res);

		$res = $this->Logs->getTypes();
		$this->assertSame(['bar' => 'bar', 'foo' => 'foo'], $res);
	}

	/**
	 * Tests the removeDuplicates method
	 *
	 * @covers ::removeDuplicates
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
		$this->assertTrue(!empty($res));

		$data = [
			'type' => 'Bar',
			'summary' => 'some text',
			'message' => 'some more text',
		];
		$log = $this->Logs->newEntity($data);
		$res = $this->Logs->save($log);
		$this->assertTrue(!empty($res));

		$data['message'] .= ' extra';
		$log = $this->Logs->newEntity($data);
		$res = $this->Logs->save($log);
		$this->assertTrue(!empty($res));

		$resBefore = $this->Logs->find()->count();
		$this->Logs->removeDuplicates();

		$resAfter = $this->Logs->find()->count();
		$this->assertSame($resBefore - 1, $resAfter, 'Res after is ' . $resAfter . ' but expected ' . ($resBefore - 1));
	}

}
