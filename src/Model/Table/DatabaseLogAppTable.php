<?php
/**
 * CakePHP DatabaseLog Plugin
 *
 * Licensed under The MIT License.
 *
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 * @link https://github.com/dereuromark/CakePHP-DatabaseLog
 */
namespace DatabaseLog\Model\Table;

use Cake\Core\Configure;
use Cake\ORM\Table;

/**
 * DatabaseLog App Model
 */
class DatabaseLogAppTable extends Table {

	/**
	 * Filter fields
	 */
	public $searchFields = array();

	/**
	* Configurations
	*/
	public $configs = array(
		'datasource' => 'default',
	);

	/**
	 * Set the default datasource to the read setup in config
	 *
	 * {@inheritDoc}
	 */
	public function __construct(array $config = []) {
		$this->configs = Configure::read('DatabaseLog');

		parent::__construct($config);
		$this->setDataSource();
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
	* Set the datasource to be used
	 *
	* if being tested, don't change, otherwise change to what we read
	 *
	 * @return void
	*/
	protected function setDataSource() {
		if ($this->connection() !== 'test') {
			$this->connection($this->configs['datasource']);
		}
	}

}
