<?php

use Cake\Core\Configure;
use Cake\Datasource\ConnectionManager;
use Cake\Http\Exception\InternalErrorException;

$connection = Configure::read('DatabaseLog.connection') ?: Configure::read('DatabaseLog.datasource');
if (!$connection) {
	$hasDatabaseLogConfig = ConnectionManager::getConfig('database_log');
	if (!$hasDatabaseLogConfig && !in_array('sqlite', PDO::getAvailableDrivers())) {
		throw new InternalErrorException('You need to either install pdo_sqlite, ' .
			'or define the `connection` name in the `DatabaseLog` config.');
	}
	if (!$hasDatabaseLogConfig) {
		ConnectionManager::setConfig('database_log', [
			'className' => 'Cake\Database\Connection',
			'driver' => 'Cake\Database\Driver\Sqlite',
			'database' => LOGS . 'database_log.sqlite',
			'encoding' => 'utf8mb4',
			'cacheMetadata' => true,
			'quoteIdentifiers' => false,
		]);
	}
}

if (Configure::read('debug') && !is_dir(LOGS)) {
	mkdir(LOGS, 0770, true);
}

if (!defined('SECOND')) {
	define('SECOND', 1);
	define('MINUTE', 60);
	define('HOUR', 3600);
	define('DAY', 86400);
	define('WEEK', 604800);
	define('MONTH', 2592000);
	define('YEAR', 31536000);
}
