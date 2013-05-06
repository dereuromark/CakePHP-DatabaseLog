<?php
App::uses('AppModel', 'Model');

class DatabaseLogAppModel extends AppModel {

	public $recursive = -1;

	/**
	* Filter fields
	*/
	public $searchFields = array();

	/**
	* Configurations
	*/
	public $configs = array(
		'write' => 'default',
		'read' => 'default',
	);

	/**
	* Set the default datasource to the read setup in config
	*/
	public function __construct($id = false, $table = null, $ds = null) {
		if (Configure::load('database_log')) {
			$this->configs = Configure::read('DatabaseLog');
		}
		parent::__construct($id, $table, $ds);
		$this->setDataSourceRead();
	}

	/**
	* Overwrite save to write to the datasource defined in config
	*/
	public function save($data = null, $validate = true, $fieldList = array()) {
		$this->setDataSourceWrite();
		$retval = parent::save($data, $validate, $fieldList);
		$this->setDataSourceRead();
		return $retval;
	}

	/**
	* Overwrite delete to delete to the datasource defined in config
	*/
	public function delete($id = null, $cascade = true) {
		$this->setDataSourceWrite();
		$retval = parent::delete($id, $cascade);
		$this->setDataSourceRead();
		return $retval;
	}

	/**
	* Overwrite find so I can do some nice things with it.
	* @param string find type
	* - last : find last record by created date
	* @param array of options
	*/
	public function find($type = 'first', $options = array()) {
		switch ($type) {
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
	public function generateFilterConditions($filter = null) {
		$retval = array();
		if ($filter) {
			foreach ($this->searchFields as $field) {
				$retval['OR']["$field LIKE"] = '%' . $filter . '%';
			}
		}
		return $retval;
	}

	/**
	* Set the datasource to be read
	* if being tested, don't change, otherwise change to what we read
	*/
	protected function setDataSourceRead() {
		if ($this->useDbConfig != 'test') {
			$this->setDataSource($this->configs['read']);
		}
	}

	/**
	* Set the datasource to be write
	* if being tested, don't change, otherwise change to what we config
	*/
	protected function setDataSourceWrite() {
		if ($this->useDbConfig != 'test') {
			$this->setDataSource($this->configs['write']);
		}
	}
}
