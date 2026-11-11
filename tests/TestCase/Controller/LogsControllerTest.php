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
use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;
use DatabaseLog\Controller\Admin\LogsController;
use PHPUnit\Framework\Attributes\CoversClass;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

#[CoversClass(LogsController::class)]
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

	public function testIndexEscapesTypeInResetButton(): void {
		$type = '<img src=x onerror=alert(1)>';
		$this->get(['prefix' => 'Admin', 'plugin' => 'DatabaseLog', 'controller' => 'Logs', 'action' => 'index', '?' => ['type' => $type]]);

		$this->assertResponseCode(200);
		$this->assertResponseNotContains($type);
		$this->assertResponseContains('&lt;img src=x onerror=alert(1)&gt;');
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
	 * CakePHP's CspMiddleware stores the nonce as `cspScriptNonce`.
	 *
	 * @return void
	 */
	public function testViewUsesCspScriptNonce(): void {
		$this->withRequestAttribute('cspScriptNonce', 'core-nonce');

		$this->get(['prefix' => 'Admin', 'plugin' => 'DatabaseLog', 'controller' => 'Logs', 'action' => 'view', '1']);

		$this->assertResponseCode(200);
		$this->assertResponseContains('<script nonce="core-nonce">');
		$this->assertResponseNotRegExp('/<script>/');
	}

	/**
	 * A custom middleware may still provide the nonce as `cspNonce`.
	 *
	 * @return void
	 */
	public function testViewFallsBackToCspNonce(): void {
		$this->withRequestAttribute('cspNonce', 'custom-nonce');

		$this->get(['prefix' => 'Admin', 'plugin' => 'DatabaseLog', 'controller' => 'Logs', 'action' => 'view', '1']);

		$this->assertResponseCode(200);
		$this->assertResponseContains('<script nonce="custom-nonce">');
		$this->assertResponseNotRegExp('/<script>/');
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

	/**
	 * Adds a middleware that sets a request attribute, like a CSP middleware would.
	 *
	 * @param string $name
	 * @param string $value
	 *
	 * @return void
	 */
	protected function withRequestAttribute(string $name, string $value): void {
		EventManager::instance()->on('Server.buildMiddleware', function (EventInterface $event, MiddlewareQueue $queue) use ($name, $value): void {
			$queue->prepend(function (ServerRequestInterface $request, RequestHandlerInterface $handler) use ($name, $value): ResponseInterface {
				return $handler->handle($request->withAttribute($name, $value));
			});
		});
	}

}
