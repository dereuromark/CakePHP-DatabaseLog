<?php

use Cake\Cache\Cache;
use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\Datasource\ConnectionManager;
use Cake\Log\Log;
use Cake\TestSuite\Fixture\SchemaLoader;
use Cake\View\View;
use DatabaseLog\DatabaseLogPlugin;
use TestApp\Application;
use TestApp\Controller\AppController;

if (!defined('DS')) {
	define('DS', DIRECTORY_SEPARATOR);
}
define('ROOT', dirname(__DIR__));
define('APP_DIR', 'src');

define('APP', rtrim(sys_get_temp_dir(), DS) . DS . APP_DIR . DS);
if (!is_dir(APP)) {
	mkdir(APP, 0770, true);
}

define('TMP', ROOT . DS . 'tmp' . DS);
if (!is_dir(TMP)) {
	mkdir(TMP, 0770, true);
}
define('TESTS', ROOT . DS . 'tests' . DS);
define('CONFIG', TESTS . 'config' . DS);

define('LOGS', TMP . 'logs' . DS);
define('CACHE', TMP . 'cache' . DS);

define('CAKE_CORE_INCLUDE_PATH', ROOT . '/vendor/cakephp/cakephp');
define('CORE_PATH', CAKE_CORE_INCLUDE_PATH . DS);
define('CAKE', CORE_PATH . APP_DIR . DS);

require dirname(__DIR__) . '/vendor/autoload.php';
require CORE_PATH . 'config/bootstrap.php';
require CAKE_CORE_INCLUDE_PATH . '/src/functions.php';

require ROOT . '/config/bootstrap.php';

Configure::write('App', [
	'namespace' => 'TestApp',
	'encoding' => 'UTF-8',
	'paths' => [
		'templates' => [ROOT . DS . 'tests' . DS . 'test_app' . DS . 'templates' . DS],
	],
]);

Configure::write('debug', true);

Configure::write('DatabaseLog.isSearchEnabled', true);

$cache = [
	'default' => [
		'engine' => 'File',
	],
	'_cake_core_' => [
		'className' => 'File',
		'prefix' => 'crud_myapp_cake_core_',
		'path' => CACHE . 'persistent/',
		'serialize' => true,
		'duration' => '+10 seconds',
	],
	'_cake_model_' => [
		'className' => 'File',
		'prefix' => 'crud_my_app_cake_model_',
		'path' => CACHE . 'models/',
		'serialize' => 'File',
		'duration' => '+10 seconds',
	],
];

$config = [
	'Log' => [
		'debug' => [
			'className' => 'DatabaseLog.Database',
		],
		'error' => [
			'className' => 'DatabaseLog.Database',
		],
	],
];
Log::setConfig($config['Log']);

Cache::setConfig($cache);

class_alias(AppController::class, 'App\Controller\AppController');
class_alias(View::class, 'App\View\AppView');
class_alias(Application::class, 'App\Application');

Plugin::getCollection()->add(new DatabaseLogPlugin());

// Ensure default test connection is defined
if (!getenv('DB_CLASS')) {
	putenv('DB_CLASS=Cake\Database\Driver\Sqlite');
	putenv('DB_URL=sqlite::memory:');
}

ConnectionManager::setConfig('test', [
	'className' => 'Cake\Database\Connection',
	'driver' => getenv('DB_CLASS') ?: null,
	'dsn' => getenv('DB_URL') ?: null,
	'timezone' => 'UTC',
	'quoteIdentifiers' => true,
	'cacheMetadata' => true,
]);

ConnectionManager::setConfig('test_database_log', [
	'className' => 'Cake\Database\Connection',
	'driver' => getenv('DB_CLASS') ?: null,
	'dsn' => getenv('DB_URL') ?: null,
	'timezone' => 'UTC',
	'quoteIdentifiers' => true,
	'cacheMetadata' => true,
]);

if (env('FIXTURE_SCHEMA_METADATA')) {
	$loader = new SchemaLoader();
	$loader->loadInternalFile(env('FIXTURE_SCHEMA_METADATA'));
}
