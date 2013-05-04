<?php
App::import('Model', 'database_log.Log');

class LogTest extends CakeTestCase {

	public $Log;

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
