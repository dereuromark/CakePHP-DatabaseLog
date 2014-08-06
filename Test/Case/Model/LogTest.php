<?php
App::uses('Log', 'DatabaseLog.Model');

class LogTest extends CakeTestCase {

	public $Log;

	public $fixtures = array('plugin.database_log.log');

	/**
	 * LogTest::setUp()
	 *
	 * @return void
	 */
	public function setUp() {
		$this->Log = ClassRegistry::init('DatabaseLog.Log');

		parent::setUp();
	}

	/**
	 * LogTest::testSave()
	 *
	 * @return void
	 */
	public function testSave() {
		$data = array(
			'type' => 'Foo',
			'message' => 'some text'
		);
		$this->Log->create();
		$res = $this->Log->save($data);
		$this->assertTrue(!empty($res));
		//$this->assertTrue(!empty($res['Log']['hostname']));
		$this->assertTrue(!empty($res['Log']['uri']));
		$this->assertTrue(!empty($res['Log']['refer']));
		$this->assertTrue(!empty($res['Log']['user_agent']));
	}

	/**
	 * LogTest::testTextSearch()
	 *
	 * @return void
	 */
	public function testTextSearch() {
		$res = $this->Log->textSearch('interesting');
		$this->assertEquals(array('MATCH (Log.message) AGAINST (\'interesting\')'), $res);

		$res = $this->Log->textSearch('type@foo');
		$this->assertEquals(array('Log.type' => 'foo'), $res);
	}

	/**
	 * LogTest::testGetTypes()
	 *
	 * @return void
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
	 * LogTest::testRemoveDuplicates()
	 *
	 * @return void
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
