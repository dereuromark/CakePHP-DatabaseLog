<?php
App::uses('LogsController', 'DatabaseLog.Controller');

class LogsControllerTest extends CakeTestCase {

	public $Logs;

	public $fixtures = array('plugin.database_log.log', 'core.cake_session');

	public function setUp() {
		$this->Logs = new TestLogsController(new CakeRequest(), new CakeResponse());
		$this->Logs->constructClasses();

		parent::setUp();
	}

	public function testIndex() {
		$this->Logs->admin_index();

		$this->assertTrue(!empty($this->Logs->viewVars['logs']));
	}

	public function testView() {
		$data = array(
			'type' => 'Bar',
			'message' => 'some more text'
		);
		$this->Logs->Log->create();
		$res = $this->Logs->Log->save($data);
		$this->Logs->admin_view($this->Logs->Log->id);

		$this->assertEquals('some more text', $this->Logs->viewVars['log']['Log']['message']);
	}

	/**
	 * LogsControllerTest::testDeleteWithoutPost()
	 *
	 * @return void
	 * @expectedException MethodNotAllowedException
	 */
	public function testDeleteWithoutPost() {
		$this->Logs->admin_delete($this->Logs->Log->id);
	}

	public function testDelete() {
		$_SERVER['REQUEST_METHOD'] = 'POST';
		$data = array(
			'type' => 'Bar',
			'message' => 'some more text'
		);
		$this->Logs->Log->create();
		$res = $this->Logs->Log->save($data);
		$this->Logs->admin_delete($this->Logs->Log->id);
	}

	public function testReset() {
		$_SERVER['REQUEST_METHOD'] = 'POST';
		$data = array(
			'type' => 'Bar',
			'message' => 'some more text'
		);
		$this->Logs->Log->create();
		$res = $this->Logs->Log->save($data);
		$this->Logs->admin_reset();

		$count = $this->Logs->Log->find('count');
		$this->assertSame(0, $count);
	}

	public function testRemoveDuplicates() {
		$this->Logs->admin_remove_duplicates();
	}

}

class TestLogsController extends LogsController {

	public $uses = array('DatabaseLog.Log');

	public $autoRender = false;

	public function redirect($url, $status = null, $exit = true) {
		$this->redirectUrl = $url;
	}
}