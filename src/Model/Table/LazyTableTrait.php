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

use Cake\Core\App;
use RuntimeException;

/**
 * A set of methods for building a database table when it is missing.
 *
 * Because the debugkit doesn't come with a pre-built SQLite database,
 * we'll need to make it as we need it.
 *
 * This trait lets us dump fixture schema into a given database at runtime.
 *
 * @mixin \DatabaseLog\Model\Table\DatabaseLogsTable
 */
trait LazyTableTrait {

	/**
	 * @var bool
	 */
	protected static $invoked = false;

	/**
	 * Ensures the tables for the given fixtures exist in the schema.
	 *
	 * If the tables do not exist, they will be created on the current model's connection.
	 *
	 * @param array $fixtures The fixture names to check and/or insert.
	 * @throws \RuntimeException When fixtures are missing/unknown/fail.
	 * @return void
	 */
	public function ensureTables(array $fixtures) {
		$connection = $this->getConnection();

		if (static::$invoked) {
			// When exceptions are encountered we try to avoid loops
			return;
		}
		static::$invoked = true;

		$schema = $connection->getSchemaCollection();
		$existing = $schema->listTables();

		foreach ($fixtures as $name) {
			$class = App::className($name, 'Test/Fixture', 'Fixture');
			if ($class === null) {
				throw new RuntimeException("Unknown fixture '$name'.");
			}
			/** @var \Cake\TestSuite\Fixture\TestFixture $fixture */
			$fixture = new $class($this->getConnection()->configName());
			$table = $fixture->table;
			if (in_array($table, $existing, true)) {
				continue;
			}
			$fixture->create($connection);
		}
	}

}
