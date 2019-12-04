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
	 * @var string
	 */
	public $modelClass = 'DatabaseLog.DatabaseLogs';

	/**
	 * @param \Cake\Event\EventInterface $event
	 *
	 * @return \Cake\Http\Response|null|void
	 */
	public function beforeRender(EventInterface $event) {
		parent::beforeRender($event);

		$this->viewBuilder()->setHelpers(['Time', 'DatabaseLog.Log']);
	}
	/**
	 * Overview action
	 *
	 * @return \Cake\Http\Response|null|void
	 */
	public function index() {
		$logs = [];
		$typesWithCount = $this->DatabaseLogs->getTypesWithCount();

		$lastErrors = $this->DatabaseLogs->find()
			->select(['summary'])
			->where(['type' => 'error'])
			->group('summary')
			->orderDesc('id')
			->limit(10)
			->disableHydration()
			->all()->toArray();

		$this->set(compact('logs', 'typesWithCount', 'lastErrors'));
	}

}
