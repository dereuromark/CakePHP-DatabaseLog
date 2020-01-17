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

use Cake\ORM\TableRegistry;
use Cake\TestSuite\IntegrationTestCase;

class DatabaseLogControllerTest extends IntegrationTestCase {

	/**
	 * @var \DatabaseLog\Model\Table\DatabaseLogsTable
	 */
	protected $Logs;

	/**
	 * Fixtures
	 *
	 * @var array
	 */
	protected $fixtures = [
		'plugin.DatabaseLog.DatabaseLogs',
		'core.Sessions',
	];

	/**
	 * Setup
	 *
	 * @return void
	 */
	public function setUp(): void {
		parent::setUp();

		$this->Logs = TableRegistry::get('DatabaseLog.DatabaseLogs');
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

		$this->get(['prefix' => 'Admin', 'plugin' => 'DatabaseLog', 'controller' => 'DatabaseLog', 'action' => 'index']);

		$this->assertResponseNotEmpty();
		$this->assertResponseCode(200);
	}

}
