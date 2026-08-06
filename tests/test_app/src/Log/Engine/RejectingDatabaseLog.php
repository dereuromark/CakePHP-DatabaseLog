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
use Stringable;

/**
 * Stands in for a table that rejects the row instead of throwing, which is what
 * DatabaseLogsTable::log() does when the save fails validation or rules.
 */
class RejectingDatabaseLog extends DatabaseLog {

	/**
	 * @var array<array{string, string, string}>
	 */
	public array $fallbacks = [];

	/**
	 * @return object
	 */
	protected function getLogsTable() {
		return new class {

			/**
			 * @param mixed $level
			 * @param mixed $message
			 * @param array<mixed> $context
			 * @return bool
			 */
			public function log($level, $message, array $context = []): bool {
				return false;
			}

		};
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
