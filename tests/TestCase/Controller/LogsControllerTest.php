<?php
/**
 * CakePHP DatabaseLog Plugin
 *
 * Licensed under The MIT License.
 *
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 * @link https://github.com/dereuromark/CakePHP-DatabaseLog
 */

namespace DatabaseLog\Test\TestCase\Controller;

use Cake\ORM\TableRegistry;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;

/**
 * @coversDefaultClass \DatabaseLog\Controller\Admin\LogsController
 */
class LogsControllerTest extends TestCase {

	use IntegrationTestTrait;

	/**
	 * @var \DatabaseLog\Model\Table\DatabaseLogsTable
	 */
	protected $Logs;

	/**
	 * Fixtures
	 *
	 * @var array
	 */
	protected array $fixtures = [
		'plugin.DatabaseLog.DatabaseLogs',
		'plugin.DatabaseLog.Sessions',
	];

	/**
	 * Setup
	 *
	 * @return void
	 */
	public function setUp(): void {
		parent::setUp();

		$this->loadPlugins(['DatabaseLog']);

		$this->Logs = TableRegistry::getTableLocator()->get('DatabaseLog.DatabaseLogs');
		if (!$this->Logs->find()->count()) {
			$this->Logs->log('warning', 'Foo Warning', ['x' => 'y']);
		}
	}

	/**
	 * Tests the index action
	 *
	 * @return void
	 */
	public function testIndex() {
		$this->disableErrorHandlerMiddleware();

		$this->get(['prefix' => 'Admin', 'plugin' => 'DatabaseLog', 'controller' => 'Logs']);

		$this->assertResponseNotEmpty();
		$this->assertResponseCode(200);
	}

	/**
	 * Tests the view action
	 *
	 * @return void
	 */
	public function testView() {
		$this->disableErrorHandlerMiddleware();

		$this->get(['prefix' => 'Admin', 'plugin' => 'DatabaseLog', 'controller' => 'Logs', 'action' => 'view', '1']);

		$this->assertResponseNotEmpty();
		$this->assertResponseCode(200);
	}

	/**
	 * Tests the delete action without POST
	 *
	 * @return void
	 */
	public function testDeleteWithoutPost() {
		$this->get(['prefix' => 'Admin', 'plugin' => 'DatabaseLog', 'controller' => 'Logs', 'action' => 'delete', '1']);

		$this->assertNoRedirect();
		$this->assertResponseCode(405);
	}

	/**
	 * Tests the delete action
	 *
	 * @return void
	 */
	public function testDelete() {
		$this->disableErrorHandlerMiddleware();

		$logModel = TableRegistry::getTableLocator()->get('DatabaseLog.DatabaseLogs');
		$count = $logModel->find()->count();

		$this->post(
			['prefix' => 'Admin', 'plugin' => 'DatabaseLog', 'controller' => 'Logs', 'action' => 'delete', 1],
		);
		$logModel = TableRegistry::getTableLocator()->get('DatabaseLog.DatabaseLogs');
		$countAfter = $logModel->find()->count();

		$this->assertSame($count - 1, $countAfter);
	}

	/**
	 * Tests the reset action without POST
	 *
	 * @return void
	 */
	public function testResetWithoutPost() {
		$this->get(['prefix' => 'Admin', 'plugin' => 'DatabaseLog', 'controller' => 'Logs', 'action' => 'reset']);

		$this->assertNoRedirect();
		$this->assertResponseCode(405);
	}

	/**
	 * Tests the reset action
	 *
	 * @return void
	 */
	public function testReset() {
		$this->disableErrorHandlerMiddleware();

		$logModel = TableRegistry::getTableLocator()->get('DatabaseLog.DatabaseLogs');
		$count = $logModel->find()->count();

		$this->assertTrue($count > 0);

		$this->post(['prefix' => 'Admin', 'plugin' => 'DatabaseLog', 'controller' => 'Logs', 'action' => 'reset']);

		$this->assertResponseSuccess();
		$this->assertRedirect();

		$count = $logModel->find()->count();

		$this->assertSame(0, $count);
	}

	/**
	 * Tests the remove duplicates action
	 *
	 * @return void
	 */
	public function testRemoveDuplicates() {
		$this->disableErrorHandlerMiddleware();

		$this->post(['prefix' => 'Admin', 'plugin' => 'DatabaseLog', 'controller' => 'Logs', 'action' => 'removeDuplicates']);

		$logModel = TableRegistry::getTableLocator()->get('DatabaseLog.DatabaseLogs');
		$count = $logModel->find()->count();

		$this->assertSame(1, $count);
	}

}
