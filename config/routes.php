<?php

use Cake\Routing\RouteBuilder;
use Cake\Routing\Router;

Router::prefix('Admin', function (RouteBuilder $routes) {
	$routes->plugin('DatabaseLog', ['path' => '/database-log'], function (RouteBuilder $routes) {
		$routes->connect('/', ['controller' => 'DatabaseLog', 'action' => 'index']);

		$routes->fallbacks();
	});
});
