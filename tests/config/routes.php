<?php

namespace DatabaseLog\Test\App\Config;

use Cake\Routing\Route\DashedRoute;
use Cake\Routing\Router;

Router::scope('/', function($routes) {
    $routes->fallbacks(DashedRoute::class);
});

Router::prefix('admin', function ($routes) {
    $routes->plugin('DatabaseLog', ['path' => '/database-log'], function ($routes) {
        $routes->fallbacks(DashedRoute::class);
    });
});
