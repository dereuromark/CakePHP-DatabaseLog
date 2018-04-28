<?php
use Cake\Routing\Router;
use Cake\Routing\Route\DashedRoute;
use Cake\Routing\RouteBuilder;

Router::prefix('admin', function (RouteBuilder $routes) {
	$routes->plugin('DatabaseLog', ['path' => '/database-log'], function (RouteBuilder $routes) {
		$routes->fallbacks(DashedRoute::class);
	});
});
