<?php
App::uses('DatabaseLogAppController', 'DatabaseLog.Controller');

class DatabaseLogController extends DatabaseLogAppController {

	public $uses = array('DatabaseLog.Log');

	/**
	 * Overview
	 *
	 * @return void
	 */
	public function admin_index() {
		$types = $this->Log->getTypes();
		$this->set(compact('types'));
	}

}
