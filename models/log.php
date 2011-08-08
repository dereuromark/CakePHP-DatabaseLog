<?php
class Log extends DatabaseLoggerAppModel {
	var $name = 'Log';
	var $displayField = 'type';
	var $searchFields = array('Log.type','Log.message','Log.created','Log.id','Log.ip','Log.hostname','Log.uri','Log.refer');
	
	function beforeSave(){
		$this->data[$this->alias]['ip'] = env('REMOTE_ADDR');
		$this->data[$this->alias]['hostname'] = env('HTTP_HOST');
		$this->data[$this->alias]['uri'] = env('REQUEST_URI');
		$this->data[$this->alias]['refer'] = env('HTTP_REFERER');
		return true;
	}
}
