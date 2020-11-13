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
use Cake\Core\Plugin;
use Cake\ORM\Table;

abstract class DatabaseLogAppTable extends Table {

	/**
	 * Filter fields
	 *
	 * @var string[]
	 */
	public $searchFields = [];

	/**
	 * Return conditions based on searchable fields and filter
	 *
	 * @param string|null $filter The filter string.
	 * @return array The generated filter conditions array.
	 */
	public function generateFilterConditions(?string $filter = null): array {
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
	 * @see \Cake\ORM\TableRegistry::get()
	 * @return string
	 */
	public static function defaultConnectionName(): string {
		return Configure::read('DatabaseLog.datasource') ?: 'database_log';
	}

	/**
	 * @return bool
	 */
	public static function isSearchEnabled() {
		$isSearchEnabled = Configure::read('DatabaseLog.isSearchEnabled');
		if ($isSearchEnabled === null) {
			return Plugin::isLoaded('Search');
		}

		return $isSearchEnabled !== false;
	}

}
