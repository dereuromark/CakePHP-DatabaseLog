<?php

use Cake\Core\Configure;
use Cake\Datasource\ConnectionManager;
use Cake\Http\Exception\InternalErrorException;

if (!Configure::read('DatabaseLog.datasource')) {
	$hasDatabaseLogConfig = ConnectionManager::getConfig('database_log');
	if (!$hasDatabaseLogConfig && !in_array('sqlite', PDO::getAvailableDrivers())) {
		throw new InternalErrorException('You need to either install pdo_sqlite, ' .
			'or define the "database_log" connection name.');
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
