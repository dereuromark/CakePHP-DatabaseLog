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
 * Logs Controller
 */
class LogsController extends AppController {

	/**
	 * Explicitly use the Log model.
	 *
	 * Fixes problems with the controller test.
	 *
	 * @var string
	 */
	public $modelClass = 'DatabaseLog.Logs';

	/**
	 * Load the TimeHelper
	 *
	 * @var array
	 */
	public $helpers = ['Time'];

	/**
	 * Setup pagination
	 *
	 * @var array
	 */
	public $paginate = [
		'order' => ['Logs.id' => 'DESC'],
		'fields' => [
			'Logs.created',
			'Logs.type',
			'Logs.message',
			'Logs.id'
		]
	];

	/**
	 * Index/Overview action
	 *
	 * @return void
	 */
	public function index() {
		$types = $this->Logs->getTypes();
		$this->set(compact('types'));

		$conditions = $this->Logs->textSearch();
		$type = $this->request->query('type');
		if ($type) {
			$conditions['type'] = $type;
		}
		$this->paginate = [
			'order' => ['created' => 'DESC'],
			'conditions' => $conditions
		];

		$this->set('logs', $this->paginate());
		$this->set('types', $this->Logs->getTypes());
	}

	/**
	 * View action
	 *
	 * @param null|int $id The log ID to view.
	 * @return void
	 */
	public function view($id = null) {
		$log = $this->Logs->get($id);
		$this->set('log', $log);
	}

	/**
	 * Delete action
	 *
	 * @param null|int $id The log ID to delete.
	 * @return \Cake\Network\Response|null
	 */
	public function delete($id = null) {
		$this->request->allowMethod('post');
		$log = $this->Logs->get($id);

		if ($this->Logs->delete($log)) {
			$this->Flash->success(__('Log deleted'));
			return $this->redirect(['action' => 'index']);
		}
		$this->Flash->error(__('Log was not deleted'));
		return $this->redirect(['action' => 'index']);
	}

	/**
	 * Reset action
	 *
	 * Deletes all log entries.
	 *
	 * @return \Cake\Network\Response|null
	 */
	public function reset() {
		$this->request->allowMethod('post');

		$this->Logs->deleteAll('1 = 1');
		return $this->redirect(['action' => 'index']);
	}

	/**
	 * Remove duplicates action
	 *
	 * @return \Cake\Network\Response|null
	 */
	public function removeDuplicates() {
		$this->request->allowMethod('post');
		
		$this->Logs->removeDuplicates();

		$this->Flash->success(__('Duplicates have been removed.'));
		return $this->redirect(['action' => 'index']);
	}

}
