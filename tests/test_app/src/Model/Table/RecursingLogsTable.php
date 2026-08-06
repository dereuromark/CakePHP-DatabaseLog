<?php
/**
 * CakePHP DatabaseLog Plugin
 *
 * Licensed under The MIT License.
 *
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 * @link https://github.com/dereuromark/CakePHP-DatabaseLog
 */

namespace TestApp\Model\Table;

use Cake\ORM\Table;
use DatabaseLog\Model\Table\LazyTableTrait;
use RuntimeException;

/**
 * Reproduces the shape of the production recursion without needing a broken cache backend:
 * the schema lookup itself calls back into ensureTables(), the way a cache engine logging its
 * own connection failure does.
 */
class RecursingLogsTable extends Table {

	use LazyTableTrait;

	/**
	 * How often the actual body ran.
	 *
	 * @var int
	 */
	public int $bodyRuns = 0;

	/**
	 * How often the body tried to reenter.
	 *
	 * @var int
	 */
	public int $reentrantAttempts = 0;

	/**
	 * @var string|null
	 */
	public ?string $failWith = null;

	/**
	 * Stands in for the real body: the schema lookup that reenters through the log engine.
	 *
	 * @param array<string> $tableNames
	 * @return void
	 */
	protected function ensureTablesInternal(array $tableNames): void {
		$this->bodyRuns++;

		if ($this->failWith !== null) {
			throw new RuntimeException($this->failWith);
		}

		$this->reentrantAttempts++;
		$this->ensureTables($tableNames);
	}

}
