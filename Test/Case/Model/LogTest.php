<?php
App::uses('Log', 'DatabaseLog.Model');

class LogTest extends CakeTestCase {

	public $Log;

	public $fixtures = array('plugin.database_log.log');

	public function setUp() {
		$this->Log = ClassRegistry::init('Log');

		parent::setUp();
	}

	public function testTextSearch() {
		$res = $this->Log->save();

	}

	public function testGetTypes() {
		$res = $this->Log->save();

	}

}
