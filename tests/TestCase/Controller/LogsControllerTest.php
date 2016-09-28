<?php
/**
 * CakePHP DatabaseLog Plugin
 *
 * Licensed under The MIT License.
 *
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 * @link https://github.com/dereuromark/CakePHP-DatabaseLog
 */
namespace DatabaseLog\TestCase\Controller;

use Cake\Network\Exception\MethodNotAllowedException;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\IntegrationTestCase;

/**
 * @coversDefaultClass LogsController
 */
class LogsControllerTest extends IntegrationTestCase {

	/**
	 * @var \DatabaseLog\Model\Table\LogsTable
	 */
	protected $Logs;

	/**
	 * Fixtures
	 *
	 * @var array
	 */
	public $fixtures = [
		'plugin.database_log.logs',
		'core.sessions'
	];

	/**
	 * Setup
	 *
	 * @return void
	 */
	public function setUp() {
		parent::setUp();

		$this->Logs = TableRegistry::get('DatabaseLog.Logs');

		/*
		if (!$this->Logs->find()->count()) {
			$this->Logs->log('warning', 'Foo Warning', ['x' => 'y']);
		}
		*/
	}

	/**
	 * Tests the index action
	 *
	 * @return void
	 */
	public function testIndex() {
		$this->get(['prefix' => 'admin', 'plugin' => 'DatabaseLog', 'controller' => 'Logs']);

		$this->assertResponseNotEmpty();
	}

	/**
	 * Tests the view action
	 *
	 * @return void
	 */
	public function testView() {
		$this->get(['prefix' => 'admin', 'plugin' => 'DatabaseLog', 'controller' => 'Logs', 'action' => 'view', '1']);

		$this->assertResponseNotEmpty();
	}

	/**
	 * Tests the delete action without POST
	 *
	 * @return void
	 */
	public function testDeleteWithoutPost() {
		$this->expectException(MethodNotAllowedException::class);

		$this->get(['prefix' => 'admin', 'plugin' => 'DatabaseLog', 'controller' => 'Logs', 'action' => 'delete', '1']);
	}

	/**
	 * Tests the delete action
	 *
	 * @return void
	 */
	public function testDelete() {
		$this->post(
			['prefix' => 'admin', 'plugin' => 'DatabaseLog', 'controller' => 'Logs', 'action' => 'delete', 1]
		);
		$logModel = TableRegistry::get('DatabaseLog.Logs');
		$count = $logModel->find()->count();

		$this->assertSame(0, $count);
	}

	/**
	 * Tests the reset action without POST
	 *
	 * @return void
	 */
	public function testResetWithoutPost() {
		$this->expectException(MethodNotAllowedException::class);

		$this->get(['prefix' => 'admin', 'plugin' => 'DatabaseLog', 'controller' => 'Logs', 'action' => 'reset']);
	}

	/**
	 * Tests the reset action
	 *
	 * @return void
	 */
	public function testReset() {
		$logModel = TableRegistry::get('DatabaseLog.Logs');
		$count = $logModel->find()->count();
		$this->assertSame(1, $count);

		$this->post(['prefix' => 'admin', 'plugin' => 'DatabaseLog', 'controller' => 'Logs', 'action' => 'reset']);
		$this->assertResponseSuccess();
		$this->assertRedirect();

		$this->assertSame(0, $count);
	}

	/**
	 * Tests the remove duplicates action
	 *
	 * @return void
	 */
	public function testRemoveDuplicates() {
		$this->post(['prefix' => 'admin', 'plugin' => 'DatabaseLog', 'controller' => 'Logs', 'action' => 'removeDuplicates']);

		$logModel = TableRegistry::get('DatabaseLog.Logs');
		$count = $logModel->find()->count();

		$this->assertSame(1, $count);
	}

}
