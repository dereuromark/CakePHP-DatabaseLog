<?php
/**
 * CakePHP DatabaseLog Plugin
 *
 * Licensed under The MIT License.
 *
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 * @link https://github.com/dereuromark/CakePHP-DatabaseLog
 */

App::uses('DatabaseLogAppController', 'DatabaseLog.Controller');

/**
 * Logs Controller
 */
class LogsController extends DatabaseLogAppController {

	/**
	 * Explicitly use the Log model.
	 *
	 * Fixes problems with the controller test.
	 *
	 * @var array
	 */
	public $uses = array('DatabaseLog.Log');

	/**
	 * Load the TimeHelper
	 *
	 * @var array
	 */
	public $helpers = array('Time');

	/**
	 * Setup pagination
	 *
	 * @var array
	 */
	public $paginate = array(
		'order' => array('Log.id' => 'DESC'),
		'fields' => array(
			'Log.created',
			'Log.type',
			'Log.message',
			'Log.id'
		)
	);

	/**
	 * Index action
	 *
	 * @param null|string $filter The filter string.
	 * @return void
	 */
	public function admin_index($filter = null) {
		if (!empty($this->data)) {
			$filter = $this->data['Log']['filter'];
		}
		$conditions = $this->Log->textSearch($filter);
		if ($type = $this->request->query('type')) {
			$conditions['type'] = $type;
		}

		$this->set('logs', $this->paginate($conditions));
		$this->set('types', $this->Log->getTypes());
		$this->set('filter', $filter);
	}

	/**
	 * View action
	 *
	 * @param null|int $id The log ID to view.
	 * @return void
	 */
	public function admin_view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid log'));
			$this->redirect(array('action' => 'index'));
		}
		$this->set('log', $this->Log->read(null, $id));
	}

	/**
	 * Delete action
	 *
	 * @param null|int $id The log ID to delete.
	 * @return void
	 */
	public function admin_delete($id = null) {
		$this->request->onlyAllow('post');
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for log'));
			$this->redirect(array('action' => 'index'));
		}
		if ($this->Log->delete($id)) {
			$this->Session->setFlash(__('Log deleted'));
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Log was not deleted'));
		$this->redirect(array('action' => 'index'));
	}

	/**
	 * Reset action
	 *
	 * Deletes all log entries.
	 *
	 * @return void
	 */
	public function admin_reset() {
		$this->request->onlyAllow('post');

		$this->Log->deleteAll('1 = 1');
		$this->redirect(array('action' => 'index'));
	}

	/**
	 * Remove duplicates action
	 *
	 * @return void
	 */
	public function admin_remove_duplicates() {
		$this->Log->removeDuplicates();

		$this->Session->setFlash(__('Duplicates have been removed.'));
		$this->redirect(array('action' => 'index'));
	}

}
