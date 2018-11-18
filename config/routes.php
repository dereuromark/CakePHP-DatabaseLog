<?php
use Cake\Routing\RouteBuilder;
use Cake\Routing\Router;

Router::prefix('admin', function (RouteBuilder $routes) {
	$routes->plugin('DatabaseLog', ['path' => '/database-log'], function (RouteBuilder $routes) {
		$routes->fallbacks();
	});
});
