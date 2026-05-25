<?php
declare(strict_types=1);

namespace DatabaseLog\Controller\Admin;

use App\Controller\AppController;
use Cake\Controller\Controller;
use Cake\Core\Configure;
use Cake\Event\EventInterface;
use Cake\Http\Exception\ForbiddenException;
use Cake\Log\Log;
use Closure;
use Throwable;

/**
 * DatabaseLogAppController
 *
 * Base controller for DatabaseLog admin.
 *
 * Authentication: by default this extends AppController to inherit the host
 * app's auth and components. Set `DatabaseLog.standalone` to `true` for an
 * isolated admin that does not depend on the host app.
 *
 * Authorization: log entries commonly contain sensitive data (stack traces
 * with credentials, request bodies, internal paths, user PII), so the
 * default policy is **deny**. The host application MUST set
 * `DatabaseLog.adminAccess` to a `Closure` that receives the current
 * request and returns literal `true` to grant access. Anything else
 * (unset, non-Closure, returns false, returns a truthy non-bool, or
 * throws) yields a 403.
 *
 * ```php
 * Configure::write('DatabaseLog.adminAccess', function (\Cake\Http\ServerRequest $request): bool {
 *     $identity = $request->getAttribute('identity');
 *     return $identity !== null && in_array('admin', (array)$identity->roles, true);
 * });
 * ```
 */
class DatabaseLogAppController extends AppController {

	use LoadHelperTrait;

	/**
	 * @return void
	 */
	public function initialize(): void {
		if (Configure::read('DatabaseLog.standalone')) {
			// Standalone mode: skip app's AppController, initialize independently
			Controller::initialize();
			$this->loadComponent('Flash');
		} else {
			// Default: inherit app's full controller setup
			parent::initialize();
		}

		$this->loadHelpers();

		// Layout configuration:
		// - null (default): Uses 'DatabaseLog.database_log' isolated Bootstrap 5 layout
		// - false: Disables plugin layout, uses app's default layout
		// - string: Uses specified layout (e.g., 'DatabaseLog.database_log' or custom)
		$layout = Configure::read('DatabaseLog.adminLayout');
		if ($layout !== false) {
			$this->viewBuilder()->setLayout($layout ?: 'DatabaseLog.database_log');
		}
	}

	/**
	 * Default-deny access gate.
	 *
	 * @param \Cake\Event\EventInterface<\Cake\Controller\Controller> $event
	 * @throws \Cake\Http\Exception\ForbiddenException When access is denied or unconfigured.
	 * @return void
	 */
	public function beforeFilter(EventInterface $event): void {
		parent::beforeFilter($event);

		// Coexist with cakephp/authorization: this gate IS the authorization
		// decision for these controllers, so silence the policy check.
		if ($this->components()->has('Authorization') && method_exists($this->components()->get('Authorization'), 'skipAuthorization')) {
			$this->components()->get('Authorization')->skipAuthorization();
		}

		$gate = Configure::read('DatabaseLog.adminAccess');
		if (!($gate instanceof Closure)) {
			throw new ForbiddenException(__d(
				'database_log',
				'DatabaseLog admin backend is not configured. Set DatabaseLog.adminAccess to a Closure that returns true for permitted callers.',
			));
		}

		try {
			$allowed = $gate($this->request) === true;
		} catch (ForbiddenException $e) {
			throw $e;
		} catch (Throwable $e) {
			Log::warning(sprintf('DatabaseLog.adminAccess threw %s: %s', $e::class, $e->getMessage()));

			throw new ForbiddenException(__d('database_log', 'DatabaseLog admin access denied.'));
		}

		if (!$allowed) {
			throw new ForbiddenException(__d('database_log', 'DatabaseLog admin access denied.'));
		}
	}

}
