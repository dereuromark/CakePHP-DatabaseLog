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

/**
 * DatabaseLog Engine
 */
class DatabaseLog extends BaseLog {

	/**
	 * Model object placeholder
	 *
	 * @var \DatabaseLog\Model\Table\DatabaseLogsTable
	 */
	public $Logs;

	/**
	 * Construct the model class
	 *
	 * @param array $config
	 */
	public function __construct($config = []) {
		parent::__construct($config);
		$model = !empty($config['model']) ? $config['model'] : 'DatabaseLog.DatabaseLogs';
		$this->Logs = TableRegistry::get($model);
	}

	/**
	 * Write the log to database
	 *
	 * @param mixed $level
	 * @param string $message
	 * @param array $context
	 * @return bool Success
	 */
	public function log($level, $message, array $context = []) {

		if ($this->config('type')) {
			$level = $this->config('type');
		}

		return $this->Logs->log($level, $message, $context);
	}

}
