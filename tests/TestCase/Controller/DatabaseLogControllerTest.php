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

use Cake\Database\Driver\Postgres;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;

class DatabaseLogControllerTest extends TestCase {

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
		$connectionConfig = $this->Logs->getConnection()->config();
		$this->skipIf($connectionConfig['driver'] === Postgres::class, 'Only for MySQL/Sqlite for now');

		$this->disableErrorHandlerMiddleware();

		$this->get(['prefix' => 'Admin', 'plugin' => 'DatabaseLog', 'controller' => 'DatabaseLog', 'action' => 'index']);

		$this->assertResponseNotEmpty();
		$this->assertResponseCode(200);
	}

}
