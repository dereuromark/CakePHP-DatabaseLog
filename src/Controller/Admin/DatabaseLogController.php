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

/**
 * @property \DatabaseLog\Model\Table\DatabaseLogsTable $DatabaseLogs
 */
class DatabaseLogController extends AppController {

	/**
	 * Explicitly use the Log model.
	 *
	 * Fixes problems with the controller test.
	 *
	 * @var string
	 */
	public $modelClass = 'DatabaseLog.DatabaseLogs';

	/**
	 * Load the TimeHelper
	 *
	 * @var array
	 */
	public $helpers = ['Time', 'DatabaseLog.Log'];

	/**
	 * Overview action
	 *
	 * @return \Cake\Http\Response|null
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
