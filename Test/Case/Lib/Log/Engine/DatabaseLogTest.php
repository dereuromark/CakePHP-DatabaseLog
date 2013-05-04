<?php
App::uses('DatabaseLog', 'Log/Engine');
App::uses('AppModel', 'Model');

/**
 * Test DatabaseLog
 *
 */
class DatabaseLogTest extends CakeTestCase {

	public $fixtures = array('plugin.database_log.log');

	public function setUp() {
		CakeLog::config('default', array('engine' => 'DatabaseLog.DatabaseLog'));
		$this->Log = ClassRegistry::init('DatabaseLog.Log');

		parent::setUp();
	}

	public function tearDown() {
		CakeLog::config('default', array('engine' => 'FileLog'));
		parent::tearDown();
	}

	/**
	 * testLogFileWriting method
	 *
	 * @return void
	 */
	public function testLogWriting() {
		$Model = ClassRegistry::init('TestLog');

		$countBefore = $this->Log->find('count');

		$Model->log('x');
		CakeLog::write('warning', 'y');
		CakeLog::write('info', 'z');

		$countAfter = $this->Log->find('count');
		debug($countAfter);
		$this->assertSame($countBefore + 3, $countAfter);
	}

}

class TestLog extends AppModel {

	public $useTable = false;

}