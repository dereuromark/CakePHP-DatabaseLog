<?php
/**
 * CakePHP DatabaseLog Plugin
 *
 * Licensed under The MIT License.
 *
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 * @link https://github.com/dereuromark/CakePHP-DatabaseLog
 */

namespace TestApp\Log\Engine;

use DatabaseLog\Log\Engine\DatabaseLog;
use RuntimeException;
use Stringable;

/**
 * Stands in for a log engine whose database cannot be reached, and records what the fallback
 * was handed instead of writing it to the process error log.
 */
class UnreachableDatabaseLog extends DatabaseLog {

	/**
	 * @var array<array{string, string, string}>
	 */
	public array $fallbacks = [];

	/**
	 * @return \DatabaseLog\Model\Table\DatabaseLogsTable
	 */
	protected function getLogsTable() {
		throw new RuntimeException('SQLSTATE[HY000] [2002] Connection refused');
	}

	/**
	 * @param string $level
	 * @param \Stringable|string $message
	 * @param string $reason
	 * @return void
	 */
	protected function fallback(string $level, Stringable|string $message, string $reason): void {
		$this->fallbacks[] = [$level, (string)$message, $reason];
	}

}
