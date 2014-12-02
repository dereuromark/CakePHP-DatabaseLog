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
 * DatabaseLog Controller
 */
class DatabaseLogController extends DatabaseLogAppController {

	/**
	 * Use a a model that differs from the controller name
	 *
	 * @var array
	 */
	public $uses = array('DatabaseLog.Log');

	/**
	 * Index/Overview action
	 *
	 * @return void
	 */
	public function admin_index() {
		$types = $this->Log->getTypes();
		$this->set(compact('types'));
	}

}
