<?php
/**
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link http://cakephp.org CakePHP(tm) Project
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */

namespace DatabaseLog\Model\Table;

use Cake\Database\Schema\TableSchema;
use PDOException;
use RuntimeException;

/**
 * A set of methods for building a database table when it is missing.
 *
 * Builds missing plugin tables from the schema configuration at runtime.
 *
 * @mixin \DatabaseLog\Model\Table\DatabaseLogsTable
 */
trait LazyTableTrait {

	/**
	 * Whether a schema lookup is currently in flight, see ensureTables().
	 *
	 * @var bool
	 */
	protected static bool $ensuring = false;

	/**
	 * Ensures the tables for the given fixtures exist in the schema.
	 *
	 * If the tables do not exist, they will be created on the current model's connection.
	 *
	 * Reentrant calls return without doing anything. Looking up the schema goes through the
	 * connection's metadata cache, and a cache engine that cannot reach its backend logs that
	 * failure - which builds the log engine, which instantiates this table, which lands back
	 * here. The table is not in the ORM locator yet at that point (the locator only stores the
	 * instance once the constructor has returned), so nothing else stops the cycle. Without
	 * this guard it repeats until the process hits its memory limit.
	 *
	 * The flag tracks "currently inside a lookup" rather than "has run once", so a failed run
	 * is still retried on the next call and a table dropped at runtime is still recreated.
	 *
	 * @param array<string> $tableNames The table names to check and/or insert.
	 *
	 * @throws \RuntimeException When fixtures are missing/unknown/fail.
	 * @return void
	 */
	public function ensureTables(array $tableNames) {
		if (static::$ensuring) {
			return;
		}

		static::$ensuring = true;
		try {
			$this->ensureTablesInternal($tableNames);
		} finally {
			static::$ensuring = false;
		}
	}

	/**
	 * @param array<string> $tableNames The table names to check and/or insert.
	 *
	 * @throws \RuntimeException When fixtures are missing/unknown/fail.
	 * @return void
	 */
	protected function ensureTablesInternal(array $tableNames): void {
		/** @var \Cake\Database\Connection $connection */
		$connection = $this->getConnection();
		$schema = $connection->getSchemaCollection();

		try {
			$existing = $schema->listTables();
		} catch (PDOException $e) {
			// Handle errors when SQLite blows up if the schema has changed.
			if (str_contains($e->getMessage(), 'schema has changed')) {
				$existing = $schema->listTables();
			} else {
				throw $e;
			}
		}

		if (in_array('database_logs', $existing, true)) {
			return;
		}

		try {
			$config = require dirname(__DIR__, 3) . '/config/schema.php';
			$driver = $connection->getDriver();
			foreach ($config as $table) {
				if (in_array($table['table'], $existing, true)) {
					continue;
				}
				if (!in_array($table['table'], $tableNames, true)) {
					continue;
				}

				$map = [
					'DatabaseLog.DatabaseLogs' => 'database_logs',
				];
				$tableName = $map[$table['table']] ?? $table['table'];

				// Use Database/Schema primitives to generate dialect specific
				// CREATE TABLE statements and run them.
				$schema = new TableSchema($tableName, $table['columns']);
				foreach ($table['constraints'] as $name => $itemConfig) {
					if (($itemConfig['type'] ?? '') === 'index') {
						$schema->addIndex($name, $itemConfig);
					} else {
						$schema->addConstraint($name, $itemConfig);
					}
				}
				foreach ($schema->createSql($connection) as $sql) {
					$driver->execute($sql);
				}
			}
		} catch (PDOException $e) {
			if (str_contains($e->getMessage(), 'unable to open')) {
				throw new RuntimeException('Could not create a SQLite database. '
					. 'Ensure that your webserver has write access to the database file and folder it is in.', 0, $e);
			}

			throw $e;
		}
	}

}
