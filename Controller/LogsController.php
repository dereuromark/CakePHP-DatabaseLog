<?php
App::uses('DatabaseLogAppController', 'DatabaseLog.Controller');

class LogsController extends DatabaseLogAppController {

	public $helpers = array('Time');

	public $paginate = array(
		'order' => array('Log.id' => 'DESC'),
		'fields' => array(
			'Log.created',
			'Log.type',
			'Log.message',
			'Log.id'
		)
	);

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

	public function admin_view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid log'));
			$this->redirect(array('action' => 'index'));
		}
		$this->set('log', $this->Log->read(null, $id));
	}

	public function admin_delete($id = null) {
		$this->request->onlyAllow('post');
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for log'));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->Log->delete($id)) {
			$this->Session->setFlash(__('Log deleted'));
			$this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash(__('Log was not deleted'));
		$this->redirect(array('action' => 'index'));
	}

	public function admin_reset() {
		$this->request->onlyAllow('post');

		$this->Log->deleteAll('1 = 1');
		$this->redirect(array('action' => 'index'));
	}

	public function admin_remove_duplicates() {
		$this->Log->removeDuplicates();

		$this->Session->setFlash(__('Duplicates have been removed.'));
		$this->redirect(array('action' => 'index'));
	}

}
