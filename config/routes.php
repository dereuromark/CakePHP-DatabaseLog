<?php
use Cake\Routing\Route\DashedRoute;
use Cake\Routing\Router;

Router::prefix('admin', function ($routes) {
	$routes->plugin('DatabaseLog', ['path' => '/database-log'], function ($routes) {
		$routes->fallbacks(DashedRoute::class);
	});
});
