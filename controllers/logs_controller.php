<?php
class LogsController extends DatabaseLoggerAppController {

	var $name = 'Logs';
	var $helpers = array('Time');

	function admin_index($filter = null) {
		if(!empty($this->data)){
			$filter = $this->data['Log']['filter'];
		}
		$conditions = $this->Log->generateFilterConditions($filter);
		$this->set('logs',$this->paginate($conditions));
		$this->set('filter', $filter);
	}

	function admin_delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for log', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->Log->delete($id)) {
			$this->Session->setFlash(__('Log deleted', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash(__('Log was not deleted', true));
		$this->redirect(array('action' => 'index'));
	}
}
