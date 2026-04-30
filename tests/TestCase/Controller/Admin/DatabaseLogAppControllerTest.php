<?php
declare(strict_types=1);

namespace DatabaseLog\Test\TestCase\Controller\Admin;

use Cake\Core\Configure;
use Cake\Http\Exception\ForbiddenException;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;
use RuntimeException;

/**
 * @uses \DatabaseLog\Controller\Admin\DatabaseLogAppController
 */
class DatabaseLogAppControllerTest extends TestCase {

	use IntegrationTestTrait;

	/**
	 * @var array<string>
	 */
	protected array $fixtures = [
		'plugin.DatabaseLog.DatabaseLogs',
	];

	/**
	 * @return void
	 */
	public function setUp(): void {
		parent::setUp();
		$this->loadPlugins(['DatabaseLog']);
	}

	/**
	 * Without a configured DatabaseLog.adminAccess gate, the backend must
	 * fail closed (403). The test bootstrap installs a permissive default;
	 * we delete it for this test only.
	 *
	 * @return void
	 */
	public function testAdminAccessUnconfiguredYields403(): void {
		$this->disableErrorHandlerMiddleware();
		Configure::delete('DatabaseLog.adminAccess');

		$this->expectException(ForbiddenException::class);
		$this->get(['prefix' => 'Admin', 'plugin' => 'DatabaseLog', 'controller' => 'DatabaseLog', 'action' => 'index']);
	}

	/**
	 * @return void
	 */
	public function testAdminAccessNonClosureYields403(): void {
		$this->disableErrorHandlerMiddleware();
		Configure::write('DatabaseLog.adminAccess', 'not a closure');

		$this->expectException(ForbiddenException::class);
		$this->get(['prefix' => 'Admin', 'plugin' => 'DatabaseLog', 'controller' => 'DatabaseLog', 'action' => 'index']);
	}

	/**
	 * @return void
	 */
	public function testAdminAccessClosureFalseYields403(): void {
		$this->disableErrorHandlerMiddleware();
		Configure::write('DatabaseLog.adminAccess', fn () => false);

		$this->expectException(ForbiddenException::class);
		$this->get(['prefix' => 'Admin', 'plugin' => 'DatabaseLog', 'controller' => 'DatabaseLog', 'action' => 'index']);
	}

	/**
	 * @return void
	 */
	public function testAdminAccessRequiresStrictTrue(): void {
		$this->disableErrorHandlerMiddleware();
		Configure::write('DatabaseLog.adminAccess', fn () => 1);

		$this->expectException(ForbiddenException::class);
		$this->get(['prefix' => 'Admin', 'plugin' => 'DatabaseLog', 'controller' => 'DatabaseLog', 'action' => 'index']);
	}

	/**
	 * @return void
	 */
	public function testAdminAccessThrowingYields403(): void {
		$this->disableErrorHandlerMiddleware();
		Configure::write('DatabaseLog.adminAccess', function (): bool {
			throw new RuntimeException('oops');
		});

		$this->expectException(ForbiddenException::class);
		$this->get(['prefix' => 'Admin', 'plugin' => 'DatabaseLog', 'controller' => 'DatabaseLog', 'action' => 'index']);
	}

	/**
	 * @return void
	 */
	public function testAdminAccessExplicitForbiddenIsRespected(): void {
		$this->disableErrorHandlerMiddleware();
		Configure::write('DatabaseLog.adminAccess', function (): bool {
			throw new ForbiddenException('custom denial reason');
		});

		$this->expectException(ForbiddenException::class);
		$this->expectExceptionMessage('custom denial reason');
		$this->get(['prefix' => 'Admin', 'plugin' => 'DatabaseLog', 'controller' => 'DatabaseLog', 'action' => 'index']);
	}

	/**
	 * @return void
	 */
	public function testAdminAccessReceivesRequest(): void {
		$received = null;
		Configure::write('DatabaseLog.adminAccess', function ($request) use (&$received): bool {
			$received = $request;

			return true;
		});

		$this->get(['prefix' => 'Admin', 'plugin' => 'DatabaseLog', 'controller' => 'DatabaseLog', 'action' => 'index']);

		$this->assertResponseOk();
		$this->assertNotNull($received);
		$this->assertStringContainsString('database', $received->getPath());
	}

}
