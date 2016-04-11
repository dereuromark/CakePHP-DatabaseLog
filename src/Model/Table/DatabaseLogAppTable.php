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
	 * @var array
	 * Filter fields
	 */
	public $searchFields = [];

	/**
	 * Return conditions based on searchable fields and filter
	 *
	 * @param string|null $filter The filter string.
	 * @return array The generated filter conditions array.
	 */
	public function generateFilterConditions($filter = null) {
		$retval = [];
		if ($filter) {
			foreach ($this->searchFields as $field) {
				$retval['OR']["$field LIKE"] = '%' . $filter . '%';
			}
		}
		return $retval;
	}

	/**
	 * Get the default connection name.
	 *
	 * This method is used to get the fallback connection name if an
	 * instance is created through the TableRegistry without a connection.
	 *
	 * @return string
	 * @see \Cake\ORM\TableRegistry::get()
	 */
	public static function defaultConnectionName() {
		return Configure::read('DatabaseLog.datasource') ?: 'database_log';
	}

}
