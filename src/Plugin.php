<?php

namespace DatabaseLog;

use Cake\Core\BasePlugin;
use Cake\Routing\RouteBuilder;

/**
 * Plugin for DatabaseLog
 */
class Plugin extends BasePlugin {

	/**
	 * @var bool
	 */
	protected $middlewareEnabled = false;

	/**
	 * @param \Cake\Routing\RouteBuilder $routes The route builder to update.
	 * @return void
	 */
	public function routes(RouteBuilder $routes): void {
		$routes->prefix('Admin', function (RouteBuilder $routes) {
			$routes->plugin('DatabaseLog', ['path' => '/database-log'], function (RouteBuilder $routes) {
				$routes->connect('/', ['controller' => 'DatabaseLog', 'action' => 'index']);

				$routes->fallbacks();
			});
		});
	}

}
