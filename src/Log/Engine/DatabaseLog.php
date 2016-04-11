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
	 * Model name placeholder
	 *
	 * @var string
	 */
	public $model;

	/**
	 * Model object placeholder
	 *
	 * @var \DatabaseLog\Model\Table\LogsTable
	 */
	public $Logs;

	/**
	 * Construct the model class
	 *
	 * @param array $config
	 */
	public function __construct($config = []) {
		parent::__construct($config);
		$this->model = !empty($config['model']) ? $config['model'] : 'DatabaseLog.Logs';
		$this->Logs = TableRegistry::get($this->model);
	}

	/**
	 * Write the log to database
	 *
	 * @param mixed $type
	 * @param string $message
	 * @param array $context
	 * @return bool Success
	 */
	public function log($type, $message, array $context = []) {
		$data = [
			'type' => $type,
			'message' => $message
		];
		$log = $this->Logs->newEntity($data);
		return (bool)$this->Logs->save($log);
	}

}
