<?php
declare(strict_types=1);

namespace DatabaseLog\Controller\Admin;

use App\Controller\AppController;
use Cake\Controller\Controller;
use Cake\Core\Configure;

/**
 * DatabaseLogAppController
 *
 * Base controller for DatabaseLog admin.
 *
 * By default, extends AppController to inherit app authentication, components, and configuration.
 * Set `DatabaseLog.standalone` to `true` for an isolated admin that doesn't depend on the host app.
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

}
