<?php
App::uses('LogsController', 'DatabaseLog.Controller');

class LogsControllerTest extends ControllerTestCase {

	/**
	 * Fixtures
	 *
	 * @var array
	 */
	public $fixtures = array(
		'plugin.database_log.log',
		'core.cake_session'
	);

	public function setUp() {
		$this->generate('DatabaseLog.Logs', array(
			'components' => array(
				'Auth',
				'Session' => array('setFlash')
			)
		));

		parent::setUp();
	}

	public function testIndex() {
		$this->testAction(
			'/logs/admin_index/',
			array('method' => 'get')
		);

		$this->assertNotEmpty($this->vars['logs']);
	}

	public function testView() {
		$this->testAction(
			'/logs/admin_view/1',
			array('method' => 'get')
		);

		$this->assertEquals('Lorem ipsum dolor sit amet', $this->vars['log']['Log']['type']);
	}

	/**
	 * LogsControllerTest::testDeleteWithoutPost()
	 *
	 * @return void
	 * @expectedException MethodNotAllowedException
	 */
	public function testDeleteWithoutPost() {
		$this->testAction(
			'/logs/admin_delete/1',
			array('method' => 'get')
		);
	}

	public function testDelete() {
		$this->testAction(
			'/logs/admin_delete/1',
			array('method' => 'post')
		);
		$logModel = ClassRegistry::init('DatabaseLog.Log');
		$count = $logModel->find('count');

		$this->assertEquals(0, $count);
	}

	/**
	 * LogsControllerTest::testResetWithoutPost()
	 *
	 * @return void
	 * @expectedException MethodNotAllowedException
	 */
	public function testResetWithoutPost() {
		$this->testAction(
			'/logs/admin_reset/',
			array('method' => 'get')
		);
	}

	public function testReset() {
		$this->testAction(
			'/logs/admin_reset/',
			array('method' => 'post')
		);
		$logModel = ClassRegistry::init('DatabaseLog.Log');
		$count = $logModel->find('count');

		$this->assertEquals(0, $count);
	}

	public function testRemoveDuplicates() {
		$this->testAction(
			'/logs/admin_remove_duplicates/',
			array('method' => 'post')
		);
		$logModel = ClassRegistry::init('DatabaseLog.Log');
		$count = $logModel->find('count');

		$this->assertEquals(1, $count);
	}

}