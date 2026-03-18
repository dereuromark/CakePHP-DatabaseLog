<?php
/**
 * CakePHP DatabaseLog Plugin
 *
 * Licensed under The MIT License.
 *
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 * @link https://github.com/dereuromark/CakePHP-DatabaseLog
 */

namespace DatabaseLog\Controller\Admin;

/**
 * @property \DatabaseLog\Model\Table\DatabaseLogsTable $DatabaseLogs
 */
class DatabaseLogController extends DatabaseLogAppController {

	/**
	 * @var string|null
	 */
	protected ?string $defaultTable = 'DatabaseLog.DatabaseLogs';

	/**
	 * Overview action
	 *
	 * @return \Cake\Http\Response|null|void
	 */
	public function index() {
		$databaseType = $this->DatabaseLogs->databaseType();
		$databaseSize = $this->DatabaseLogs->databaseSize();

		$typesWithCount = $this->DatabaseLogs->getTypesWithCount();

		$lastErrors = $this->DatabaseLogs->find()
			->select(['summary'])
			->where(['type' => 'error'])
			->groupBy('summary')
			->orderByDesc('MAX(id)')
			->limit(10)
			->disableHydration()
			->all()->toArray();

		$this->set(compact('typesWithCount', 'lastErrors', 'databaseType', 'databaseSize'));
	}

}
