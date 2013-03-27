<?php
class Log extends DatabaseLoggerAppModel {
	var $name = 'Log';
	var $displayField = 'type';
	var $searchFields = array('Log.type');
	
	function beforeSave(){
		$this->data[$this->alias]['ip'] = env('REMOTE_ADDR');
		$this->data[$this->alias]['hostname'] = env('HTTP_HOST');
		$this->data[$this->alias]['uri'] = env('REQUEST_URI');
		$this->data[$this->alias]['refer'] = env('HTTP_REFERER');
		return true;
	}
	
	/**
	* Return a text search on message
	*/
	function textSearch($query = null){
		if($query){
			if(strpos($query, 'type:') === 0){
				$query = str_replace('type:','', $query);
				return $this->generateFilterConditions($query);
			} else {
				$escapedQuery = $this->getDataSource()->value($query);
				return array("MATCH ({$this->alias}.message) AGAINST ($escapedQuery)");
			}
		}
		return array();
	}
}
