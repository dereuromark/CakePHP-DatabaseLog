<?php

namespace DatabaseLog\Test\App\Config;

use Cake\Routing\Router;
use Cake\Routing\Route\DashedRoute;

Router::prefix('admin', function ($routes) {
	$routes->plugin('DatabaseLog', ['path' => '/database-log'], function ($routes) {
		$routes->fallbacks(DashedRoute::class);
	});
});
