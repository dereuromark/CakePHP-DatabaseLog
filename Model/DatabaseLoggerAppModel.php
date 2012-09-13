<?php
class DatabaseLoggerAppModel extends AppModel {
	var $recursive = -1;
	/**
	* Filter fields
	*/
	var $searchFields = array();
	
	/**
	* Overwrite find so I can do some nice things with it.
	* @param string find type
	* - last : find last record by created date
	* @param array of options
	*/
	function find($type, $options = array()){
		switch($type){
		case 'last':
			$options = array_merge(
				$options,
				array('order' => "{$this->alias}.{$this->primaryKey} DESC")
				);
			return parent::find('first', $options);    
		default: 
			return parent::find($type, $options);
		}
	}
	
	/**
	* return conditions based on searchable fields and filter
	* @param string filter
	* @return conditions array
	*/
	function generateFilterConditions($filter = null){
		$retval = array();
		if($filter){
			foreach($this->searchFields as $field){
				$retval['OR']["$field LIKE"] =  '%' . $filter . '%'; 
			}
		}
		return $retval;
	}
}
?>