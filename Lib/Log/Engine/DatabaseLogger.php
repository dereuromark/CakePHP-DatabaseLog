<?php
/**
* Database logger
* @author Nick Baker 
* @version 1.0
* @license MIT

# Setup

in app/config/bootstrap.php add the following

CakeLog::config('database', array(
	'engine' => 'DatabaseLogger.DatabaseLogger',
	'model' => 'CustomLogModel' //'DatabaseLogger.Log' by default
));

*/
App::import('Core', 'ClassRegistry');
App::uses('CakeLogInterface','Log');
class DatabaseLogger implements CakeLogInterface{
	
	/**
	* Model name placeholder
	*/
	var $model = null;
	
	/**
	* Model object placeholder
	*/
	var $Log = null;
	
	/**
	* Contruct the model class
	*/
	function __construct($options = array()){
		$this->model = isset($options['model']) ? $options['model'] : 'DatabaseLogger.Log';
		$this->Log = ClassRegistry::init($this->model);
	}
	
	/**
	* Write the log to database
	*/
	function write($type, $message){
		$this->Log->save(array(
			'type' => $type,
			'message' => $message
		));
	}
}
?>