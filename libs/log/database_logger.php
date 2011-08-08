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
class DatabaseLogger {
	
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
		
		App::import('Model', $this->model);
		if(strpos($this->model, ".")){
			list($ignore, $this->model) = explode(".", $this->model);
		}
		eval("\$this->Log = new {$this->model}();");
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