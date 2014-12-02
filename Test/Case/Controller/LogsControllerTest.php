<?php
/**
 * CakePHP DatabaseLog Plugin
 *
 * Licensed under The MIT License.
 *
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 * @link https://github.com/dereuromark/CakePHP-DatabaseLog
 */

App::uses('LogsController', 'DatabaseLog.Controller');

/**
 * LogsController Test
 *
 * @coversDefaultClass LogsController
 */
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

	/**
	 * Setup
	 *
	 * @return void
	 */
	public function setUp() {
		$this->generate('DatabaseLog.Logs', array(
			'components' => array(
				'Auth',
				'Session' => array('setFlash')
			)
		));

		parent::setUp();
	}

	/**
	 * Tests the index action
	 *
	 * @return void
	 * @covers ::admin_index
	 */
	public function testIndex() {
		$this->testAction(
			'/logs/admin_index/',
			array('method' => 'get')
		);

		$this->assertNotEmpty($this->vars['logs']);
	}

	/**
	 * Tests the view action
	 *
	 * @return void
	 * @covers ::admin_view
	 */
	public function testView() {
		$this->testAction(
			'/logs/admin_view/1',
			array('method' => 'get')
		);

		$this->assertEquals('Lorem ipsum dolor sit amet', $this->vars['log']['Log']['type']);
	}

	/**
	 * Tests the delete action without POST
	 *
	 * @return void
	 * @expectedException MethodNotAllowedException
	 * @covers ::admin_delete
	 */
	public function testDeleteWithoutPost() {
		$this->testAction(
			'/logs/admin_delete/1',
			array('method' => 'get')
		);
	}


	/**
	 * Tests the delete action
	 *
	 * @return void
	 * @covers ::admin_delete
	 */
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
	 * Tests the reset action without POST
	 *
	 * @return void
	 * @expectedException MethodNotAllowedException
	 * @covers ::admin_reset
	 */
	public function testResetWithoutPost() {
		$this->testAction(
			'/logs/admin_reset/',
			array('method' => 'get')
		);
	}

	/**
	 * Tests the reset action
	 *
	 * @return void
	 * @covers ::admin_reset
	 */
	public function testReset() {
		$this->testAction(
			'/logs/admin_reset/',
			array('method' => 'post')
		);
		$logModel = ClassRegistry::init('DatabaseLog.Log');
		$count = $logModel->find('count');

		$this->assertEquals(0, $count);
	}

	/**
	 * Tests the remove duplicates action
	 *
	 * @return void
	 * @covers ::admin_remove_duplicates
	 */
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