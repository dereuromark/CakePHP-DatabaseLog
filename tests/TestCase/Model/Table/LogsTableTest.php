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

use DatabaseLog\Model\Table\LogsTable;

/**
 * Log Test
 *
 *
 * @coversDefaultClass Log
 */
class LogsTableTest extends TestCase {

	/**
	 * @var \DatabaseLog\Model\Table\LogsTable
	 */
	public $Logs;

	/**
	 * @var array
	 */
	public $fixtures = array(
		'plugin.database_log.logs'
	);

	/**
	 * Setup
	 *
	 * @return void
	 */
	public function setUp() {
		$this->Logs = TableRegistry::get('DatabaseLog.Logs');

		parent::setUp();
	}

	/**
	 * Tests the save method
	 *
	 * @return void
	 * @covers ::save
	 */
	public function testSave() {
		$data = array(
			'type' => 'Foo',
			'message' => 'some text'
		);
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
		$this->assertEquals(array('MATCH (Logs.message) AGAINST (\'interesting\')'), $res);

		$res = $this->Logs->textSearch('type@foo');
		$this->assertEquals(array('Log.type' => 'foo'), $res);
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

		$data = array(
			'type' => 'Foo',
			'message' => 'some text'
		);
		$log = $this->Logs->newEntity($data);
		$res = $this->Logs->save($log);
		$this->assertTrue(!empty($res));

		$data = array(
			'type' => 'Bar',
			'message' => 'some more text'
		);
		$log = $this->Logs->newEntity($data);
		$res = $this->Logs->save($log);
		$this->assertTrue(!empty($res));

		$res = $this->Logs->getTypes();
		$this->assertSame(array('Bar', 'Foo'), $res);
	}

	/**
	 * Tests the removeDuplicates method
	 *
	 * @return void
	 * @covers ::removeDuplicates
	 */
	public function testRemoveDuplicates() {
		$data = array(
			'type' => 'Foo',
			'message' => 'some text'
		);
		$log = $this->Logs->newEntity($data);
		$res = $this->Logs->save($log);
		$this->assertTrue(!empty($res));

		$data = array(
			'type' => 'Bar',
			'message' => 'some more text'
		);
		$log = $this->Logs->newEntity($data);
		$res = $this->Logs->save($log);
		$this->assertTrue(!empty($res));

		$log = $this->Logs->newEntity($data);
		$res = $this->Logs->save($log);
		$this->assertTrue(!empty($res));

		$resBefore = $this->Logs->find()->count();
		$res = $this->Logs->removeDuplicates();

		$resAfter = $this->Logs->find()->count();
		$this->assertSame($resBefore - 1, $resAfter);
	}

}
