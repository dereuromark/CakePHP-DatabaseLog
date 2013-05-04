<?php
App::uses('LogsController', 'DatabaseLog.Controller');

class LogsControllerTest extends CakeTestCase {

	public $Logs;

	public function setUp() {
		$this->Logs = new TestLogsController(new CakeRequest(), new CakeResponse());
		$this->Logs->constructClasses();

		parent::setUp();
	}

	public function testRemoveDuplicates() {
		$this->Logs->Log->create();
	}

}

class TestLogsController extends LogsController {

	public $uses = array('DatabaseLog.Log');

	public $autoRender = false;

	public function redirect($url, $status = null, $exit = true) {
		$this->redirectUrl = $url;
	}
}