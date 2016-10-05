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
class LogsTableTest extends TestCase {

	/**
	 * @var \DatabaseLog\Model\Table\DatabaseLogsTable
	 */
	public $Logs;

	/**
	 * @var array
	 */
	public $fixtures = [
		'plugin.database_log.database_logs'
	];

	/**
	 * Setup
	 *
	 * @return void
	 */
	public function setUp() {
		$this->Logs = TableRegistry::get('DatabaseLog.DatabaseLogs');

		parent::setUp();
	}

	/**
	 * Tests the save method
	 *
	 * @return void
	 */
	public function testSave() {
		$data = [
			'type' => 'Foo',
			'message' => 'some text'
		];
		$log = $this->Logs->newEntity($data);
		$res = $this->Logs->save($log);
		$this->assertTrue(!empty($res));
	}

	/**
	 * Tests the textSearch method
	 *
	 * @return void
	 * @covers ::textSearch
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
	 * @return void
	 * @covers ::getTypes
	 */
	public function testGetTypes() {
		Cache::delete('database_log_types');

		$this->Logs->deleteAll('1=1');

		$data = [
			'type' => 'Foo',
			'message' => 'some text'
		];
		$log = $this->Logs->newEntity($data);
		$res = $this->Logs->save($log);
		$this->assertTrue(!empty($res));

		$data = [
			'type' => 'Bar',
			'message' => 'some more text'
		];
		$log = $this->Logs->newEntity($data);
		$res = $this->Logs->save($log);
		$this->assertTrue(!empty($res));

		$res = $this->Logs->getTypes();
		$this->assertSame(['Bar', 'Foo'], $res);
	}

	/**
	 * Tests the removeDuplicates method
	 *
	 * @return void
	 * @covers ::removeDuplicates
	 */
	public function testRemoveDuplicates() {
		$data = [
			'type' => 'Foo',
			'message' => 'some text'
		];
		$log = $this->Logs->newEntity($data);
		$res = $this->Logs->save($log);
		$this->assertTrue(!empty($res));

		$data = [
			'type' => 'Bar',
			'message' => 'some more text'
		];
		$log = $this->Logs->newEntity($data);
		$res = $this->Logs->save($log);
		$this->assertTrue(!empty($res));

		$log = $this->Logs->newEntity($data);
		$res = $this->Logs->save($log);
		$this->assertTrue(!empty($res));

		$resBefore = $this->Logs->find()->count();
		$this->Logs->removeDuplicates();

		$resAfter = $this->Logs->find()->count();
		$this->assertSame($resBefore - 1, $resAfter);
	}

}
