<?php
/**
 * CakePHP DatabaseLog Plugin
 *
 * Licensed under The MIT License.
 *
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 * @link https://github.com/dereuromark/CakePHP-DatabaseLog
 */

App::uses('ClassRegistry', 'Utility');
App::uses('CakeLogInterface', 'Log');

/**
 * DatabaseLog Engine
 */
class DatabaseLog implements CakeLogInterface{

	/**
	 * Model name placeholder
	 */
	public $model = null;

	/**
	 * Model object placeholder
	 */
	public $Log = null;

	/**
	 * Construct the model class
	 */
	public function __construct($options = array()) {
		$this->model = isset($options['model']) ? $options['model'] : 'DatabaseLog.Log';
		$this->Log = ClassRegistry::init($this->model);
	}

	/**
	 * Write the log to database
	 *
	 * @param $type
	 * @param $message
	 * @return boolean Success
	 */
	public function write($type, $message) {
		$this->Log->create();
		return (bool)$this->Log->save(array(
			'type' => $type,
			'message' => $message
		));
	}
}
