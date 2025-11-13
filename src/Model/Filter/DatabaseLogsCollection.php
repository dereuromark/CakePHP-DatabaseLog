<?php
/**
 * CakePHP DatabaseLog Plugin
 *
 * Licensed under The MIT License.
 *
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 * @link https://github.com/dereuromark/CakePHP-DatabaseLog
 */

namespace DatabaseLog\Model\Filter;

use Search\Model\Filter\FilterCollection;

/**
 * DatabaseLogs Filter Collection
 *
 * Configures search filters for the DatabaseLogs table.
 * This class is used by Search plugin 7.x to define available filters.
 */
class DatabaseLogsCollection extends FilterCollection {

	/**
	 * Initialize search filters
	 *
	 * @return void
	 */
	public function initialize(): void {
		$this
			->value('type')
			->like('search', [
				'fields' => ['DatabaseLogs.summary'],
				'before' => true,
				'after' => true,
			]);
	}

}
