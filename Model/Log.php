<?php
App::uses('DatabaseLoggerAppModel', 'DatabaseLogger.Model');
class Log extends DatabaseLoggerAppModel {

	public $displayField = 'type';

	public $searchFields = array('Log.type');

	public function beforeSave($options = array()) {
		$this->data[$this->alias]['ip'] = env('REMOTE_ADDR');
		$this->data[$this->alias]['hostname'] = env('HTTP_HOST');
		$this->data[$this->alias]['uri'] = env('REQUEST_URI');
		$this->data[$this->alias]['refer'] = env('HTTP_REFERER');

		return parent::beforeSave($options);
	}

	/**
	* Return a text search on message
	*
	* @return array Results
	*/
	public function textSearch($query = null){
		if($query){
			if(strpos($query, 'type@') === 0){
				$query = str_replace('type@','', $query);
				return array('Log.type' => $query);
			} else {
				$escapedQuery = $this->getDataSource()->value($query);
				return array("MATCH ({$this->alias}.message) AGAINST ($escapedQuery)");
			}
		}
		return array();
	}

	/**
	* Return all the unique types
	*
	* @return array Types
	*/
	public function getTypes(){
		$cache_key = 'database_log_types';
		if($retval = Cache::read($cache_key)){
			return $retval;
		}
		$retval = $this->find('all', array(
			'fields' => array('DISTINCT Log.type'),
			'order' => array('Log.type ASC')
		));
		$retval = Hash::extract($retval,'{n}.Log.type');
		Cache::write($cache_key, $retval);
		return $retval;
	}

}
