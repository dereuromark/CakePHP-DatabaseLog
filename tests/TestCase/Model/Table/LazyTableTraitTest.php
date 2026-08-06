<?php
/**
 * CakePHP DatabaseLog Plugin
 *
 * Licensed under The MIT License.
 *
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 * @link https://github.com/dereuromark/CakePHP-DatabaseLog
 */

namespace DatabaseLog\Test\TestCase\Model\Table;

use Cake\TestSuite\TestCase;
use RuntimeException;
use TestApp\Model\Table\RecursingLogsTable;

class LazyTableTraitTest extends TestCase {

	/**
	 * A schema lookup that ends up calling back into ensureTables() must not recurse.
	 *
	 * This is what a cache engine failure does in production: looking up the schema goes
	 * through the metadata cache, the cache engine logs that it cannot reach its backend, the
	 * log engine instantiates the logs table, and initialize() lands back in ensureTables().
	 * The ORM locator does not break the cycle because it only stores the instance once the
	 * constructor has returned.
	 *
	 * @return void
	 */
	public function testEnsureTablesDoesNotRecurse() {
		$table = new RecursingLogsTable();

		$table->ensureTables(['DatabaseLog.DatabaseLogs']);

		$this->assertSame(1, $table->bodyRuns, 'The reentrant call should have returned immediately.');
		$this->assertSame(1, $table->reentrantAttempts, 'The nested call should have been attempted once.');
	}

	/**
	 * The guard tracks "currently inside a lookup", not "has run once", so a later call still
	 * does its work. A once-per-process flag would break table recreation after a drop.
	 *
	 * @return void
	 */
	public function testGuardIsReleasedAfterCompletion() {
		$table = new RecursingLogsTable();

		$table->ensureTables(['DatabaseLog.DatabaseLogs']);
		$table->ensureTables(['DatabaseLog.DatabaseLogs']);

		$this->assertSame(2, $table->bodyRuns);
	}

	/**
	 * A failed lookup must release the guard too, otherwise one transient error would disable
	 * table creation for the rest of the process.
	 *
	 * @return void
	 */
	public function testGuardIsReleasedAfterFailure() {
		$table = new RecursingLogsTable();
		$table->failWith = 'schema unavailable';

		try {
			$table->ensureTables(['DatabaseLog.DatabaseLogs']);
			$this->fail('Expected the failure to propagate.');
		} catch (RuntimeException $e) {
			$this->assertSame('schema unavailable', $e->getMessage());
		}

		$table->failWith = null;
		$table->ensureTables(['DatabaseLog.DatabaseLogs']);

		$this->assertSame(2, $table->bodyRuns);
	}

}
