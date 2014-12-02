<?php
/**
 * CakePHP DatabaseLog Plugin
 *
 * Licensed under The MIT License.
 *
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 * @link https://github.com/dereuromark/CakePHP-DatabaseLog
 */

App::uses('AppModel', 'Model');

/**
 * DatabaseLog App Model
 */
class DatabaseLogAppModel extends AppModel {

	/**
	 * Set Recursive to -1
	 *
	 * @var int
	 */
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
	 *
	 * {@inheritDoc}
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
	 *
	 * {@inheritDoc}
	 */
	public function save($data = null, $validate = true, $fieldList = array()) {
		$this->setDataSourceWrite();
		$retval = parent::save($data, $validate, $fieldList);
		$this->setDataSourceRead();
		return $retval;
	}

	/**
	 * Overwrite delete to delete to the datasource defined in config
	 *
	 * {@inheritDoc}
	 */
	public function delete($id = null, $cascade = true) {
		$this->setDataSourceWrite();
		$retval = parent::delete($id, $cascade);
		$this->setDataSourceRead();
		return $retval;
	}

	/**
	 * Overwrite find so I can do some nice things with it.
	 *
	 * Type 'last' finds the last record by created date.
	 *
	 * {@inheritDoc}
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
	 * Return conditions based on searchable fields and filter
	 *
	 * @param string $filter The filter string.
	 * @return array The generated filter conditions array.
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
		if ($this->useDbConfig !== 'test') {
			$this->setDataSource($this->configs['read']);
		}
	}

	/**
	* Set the datasource to be write
	* if being tested, don't change, otherwise change to what we config
	*/
	protected function setDataSourceWrite() {
		if ($this->useDbConfig !== 'test') {
			$this->setDataSource($this->configs['write']);
		}
	}
}
