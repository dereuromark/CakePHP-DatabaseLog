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

use Cake\Event\EventInterface;
use Cake\Event\EventManager;
use Cake\Http\MiddlewareQueue;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

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

		$this->get(['prefix' => 'Admin', 'plugin' => 'DatabaseLog', 'controller' => 'DatabaseLog', 'action' => 'index']);

		$this->assertResponseNotEmpty();
		$this->assertResponseCode(200);
	}

	/**
	 * External scripts (Chart.js, Bootstrap) need the nonce too under a nonce-only CSP.
	 *
	 * @return void
	 */
	public function testIndexAddsCspNonceToScripts(): void {
		$this->Logs->log('warning', 'Recent Warning');
		EventManager::instance()->on('Server.buildMiddleware', function (EventInterface $event, MiddlewareQueue $queue): void {
			$queue->prepend(function (ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {
				return $handler->handle($request->withAttribute('cspScriptNonce', 'core-nonce'));
			});
		});

		$this->get(['prefix' => 'Admin', 'plugin' => 'DatabaseLog', 'controller' => 'DatabaseLog', 'action' => 'index']);

		$this->assertResponseCode(200);
		$this->assertResponseContains('<script nonce="core-nonce" src="https://cdn.jsdelivr.net/npm/chart.js');
		$this->assertResponseContains('<script nonce="core-nonce" src="https://cdn.jsdelivr.net/npm/bootstrap');
		$this->assertResponseNotRegExp('/<script(?![^>]*nonce=)/');
	}

	/**
	 * Ensures the default backend path remains `/admin/database-log` (BC).
	 *
	 * @return void
	 */
	public function testDefaultRoutePath() {
		$url = Router::url([
			'prefix' => 'Admin',
			'plugin' => 'DatabaseLog',
			'controller' => 'DatabaseLog',
			'action' => 'index',
		]);

		$this->assertSame('/admin/database-log', $url);
	}

}
