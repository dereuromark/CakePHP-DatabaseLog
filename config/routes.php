<?php
/**
 * @var \Cake\Routing\RouteBuilder $routes
 */

use Cake\Routing\RouteBuilder;

$routes->prefix('Admin', function (RouteBuilder $routes) {
	$routes->plugin('DatabaseLog', ['path' => '/database-log'], function (RouteBuilder $routes) {
		$routes->connect('/', ['controller' => 'DatabaseLog', 'action' => 'index']);

		$routes->fallbacks();
	});
});
