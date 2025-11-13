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

use App\Controller\AppController;
use Cake\Event\EventInterface;

/**
 * @property \DatabaseLog\Model\Table\DatabaseLogsTable $DatabaseLogs
 */
class DatabaseLogController extends AppController {

	/**
	 * Explicitly use the Log model.
	 *
	 * @var string|null
	 */
	protected ?string $modelClass = 'DatabaseLog.DatabaseLogs';

	/**
	 * @var string|null
	 */
	protected ?string $defaultTable = 'DatabaseLog.DatabaseLogs';

	/**
	 * @param \Cake\Event\EventInterface $event
	 *
	 * @return void
	 */
	public function beforeRender(EventInterface $event): void {
		parent::beforeRender($event);

		$this->viewBuilder()->addHelpers(['Time', 'DatabaseLog.Log']);
	}

	/**
	 * Overview action
	 *
	 * @return \Cake\Http\Response|null|void
	 */
	public function index() {
		$databaseType = $this->DatabaseLogs->databaseType();
		$databaseSize = $this->DatabaseLogs->databaseSize();

		$logs = [];
		$typesWithCount = $this->DatabaseLogs->getTypesWithCount();

		$lastErrors = $this->DatabaseLogs->find()
			->select(['summary'])
			->where(['type' => 'error'])
			->groupBy('summary')
			->orderByDesc('MAX(id)')
			->limit(10)
			->disableHydration()
			->all()->toArray();

		$this->set(compact('logs', 'typesWithCount', 'lastErrors', 'databaseType', 'databaseSize'));
	}

}
