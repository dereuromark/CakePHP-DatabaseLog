<?php
/**
* Database logger
* @author Nick Baker
* @version 1.0
* @license MIT

# Setup

in app/config/bootstrap.php add the following

CakeLog::config('database', array(
	'engine' => 'DatabaseLog.DatabaseLog',
	'model' => 'CustomLogModel' //'DatabaseLog.Log' by default
));

*/
App::uses('ClassRegistry', 'Utility');
App::uses('CakeLogInterface', 'Log');

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
