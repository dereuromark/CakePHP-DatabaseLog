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

/**
 * DatabaseLog Engine
 */
class DatabaseLog extends BaseLog {

	/**
	 * Model object
	 *
	 * @var \DatabaseLog\Model\Table\DatabaseLogsTable
	 */
	public $Logs;

	/**
	 * Construct the model class
	 *
	 * @param array<string, mixed> $config
	 */
	public function __construct($config = []) {
		parent::__construct($config);
		$model = !empty($config['model']) ? $config['model'] : 'DatabaseLog.DatabaseLogs';
		/** @var \DatabaseLog\Model\Table\DatabaseLogsTable $Logs */
		$Logs = TableRegistry::getTableLocator()->get($model);
		$this->Logs = $Logs;
	}

	/**
	 * Write the log to database
	 *
	 * @param string $level
	 * @param \Stringable|string $message
	 * @param array<mixed> $context
	 * @return void
	 */
	public function log($level, string|Stringable $message, array $context = []): void {
		if ($this->getConfig('type')) {
			$level = $this->getConfig('type');
		} elseif ($this->getConfig('file')) {
			$level = $this->getConfig('file');
		}

		$this->Logs->log($level, $message, $context);
	}

}
