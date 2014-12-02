<?php
/**
 * CakePHP DatabaseLog Plugin
 *
 * Licensed under The MIT License.
 *
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 * @link https://github.com/dereuromark/CakePHP-DatabaseLog
 */

App::uses('Log', 'DatabaseLog.Model');

/**
 * Log Test
 *
 *
 * @coversDefaultClass Log
 */
class LogTest extends CakeTestCase {

	/**
	 * Model under test
	 *
	 * @var
	 */
	public $Log;

	/**
	 * Fixtures
	 *
	 * @var array
	 */
	public $fixtures = array(
		'plugin.database_log.log'
	);

	/**
	 * Setup
	 *
	 * @return void
	 */
	public function setUp() {
		$this->Log = ClassRegistry::init('DatabaseLog.Log');

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
		$this->Log->create();
		$res = $this->Log->save($data);
		$this->assertTrue(!empty($res));
	}

	/**
	 * Tests the textSearch method
	 *
	 * @return void
	 * @covers ::textSearch
	 */
	public function testTextSearch() {
		$res = $this->Log->textSearch('interesting');
		$this->assertEquals(array('MATCH (Log.message) AGAINST (\'interesting\')'), $res);

		$res = $this->Log->textSearch('type@foo');
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

		$this->Log->deleteAll('1=1');

		$data = array(
			'type' => 'Foo',
			'message' => 'some text'
		);
		$this->Log->create();
		$res = $this->Log->save($data);
		$this->assertTrue(!empty($res));

		$data = array(
			'type' => 'Bar',
			'message' => 'some more text'
		);
		$this->Log->create();
		$res = $this->Log->save($data);
		$this->assertTrue(!empty($res));

		$res = $this->Log->getTypes();
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
		$this->Log->create();
		$res = $this->Log->save($data);
		$this->assertTrue(!empty($res));

		$data = array(
			'type' => 'Bar',
			'message' => 'some more text'
		);
		$this->Log->create();
		$res = $this->Log->save($data);
		$this->assertTrue(!empty($res));

		$this->Log->create();
		$res = $this->Log->save($data);
		$this->assertTrue(!empty($res));

		$resBefore = $this->Log->find('count');
		$res = $this->Log->removeDuplicates();

		$resAfter = $this->Log->find('count');
		$this->assertSame($resBefore - 1, $resAfter);
	}

}
