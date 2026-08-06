<?php
/**
 * CakePHP DatabaseLog Plugin
 *
 * Licensed under The MIT License.
 *
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 * @link https://github.com/dereuromark/CakePHP-DatabaseLog
 */

namespace DatabaseLog\Log\Engine;

use Cake\Log\Engine\BaseLog;
use Cake\ORM\TableRegistry;
use Stringable;
use Throwable;

/**
 * DatabaseLog Engine
 */
class DatabaseLog extends BaseLog {

	/**
	 * Model object
	 *
	 * Resolved on first write rather than in the constructor, so that an unreachable database
	 * cannot make constructing the log engine fail. Log engines get built lazily from inside
	 * arbitrary code paths, including ones that are themselves reporting a failure.
	 *
	 * Deliberately not natively typed: the `model` config may point at any table that exposes a
	 * compatible `log()` method, and it does not have to extend DatabaseLogsTable.
	 *
	 * @var \DatabaseLog\Model\Table\DatabaseLogsTable|null
	 */
	public $Logs;

	/**
	 * Write the log to database
	 *
	 * Never throws. A logger that can take the application down turns any outage into a larger
	 * one, and the caller of `Log::error()` has no way to handle it. When the write cannot
	 * happen the entry goes to `error_log()` instead, so the information is not lost.
	 *
	 * @param string $level
	 * @param \Stringable|string $message
	 * @param array<mixed> $context
	 * @return void
	 */
	public function log($level, Stringable|string $message, array $context = []): void {
		if ($this->getConfig('type')) {
			$level = $this->getConfig('type');
		} elseif ($this->getConfig('file')) {
			$level = $this->getConfig('file');
		}

		try {
			$this->getLogsTable()->log($level, $message, $context);
		} catch (Throwable $e) {
			$this->fallback((string)$level, $message, $e);
		}
	}

	/**
	 * @return \DatabaseLog\Model\Table\DatabaseLogsTable
	 */
	protected function getLogsTable() {
		if ($this->Logs !== null) {
			return $this->Logs;
		}

		$model = $this->getConfig('model') ?: 'DatabaseLog.DatabaseLogs';
		/** @var \DatabaseLog\Model\Table\DatabaseLogsTable $Logs */
		$Logs = TableRegistry::getTableLocator()->get($model);

		return $this->Logs = $Logs;
	}

	/**
	 * Last resort when the database is not writable.
	 *
	 * @param string $level
	 * @param \Stringable|string $message
	 * @param \Throwable $exception
	 * @return void
	 */
	protected function fallback(string $level, Stringable|string $message, Throwable $exception): void {
		error_log(sprintf(
			'[DatabaseLog fallback][%s] %s | could not write to database: %s',
			$level,
			(string)$message,
			$exception->getMessage(),
		));
	}

}
