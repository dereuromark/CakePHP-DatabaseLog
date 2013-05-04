<?php
App::uses('LogsController', 'DatabaseLogger.Controller');

class TestLogsController extends LogsController {

	public $autoRender = false;

	public function redirect($url, $status = null, $exit = true) {
		$this->redirectUrl = $url;
	}
}

class LogsControllerTest extends CakeTestCase {

	public function setUp() {
		$this->Logs = new TestLogsController();
		$this->Logs->constructClasses();
	}


}
