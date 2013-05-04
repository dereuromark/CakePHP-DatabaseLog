<?php
App::uses('LogsController', 'DatabaseLogger.Controller');

class TestLogsController extends LogsController {

	var $autoRender = false;

	function redirect($url, $status = null, $exit = true) {
		$this->redirectUrl = $url;
	}
}

class LogsControllerTest extends CakeTestCase {

	function setUp() {
		$this->Logs =& new TestLogsController();
		$this->Logs->constructClasses();
	}


}
