<?php
App::uses('DatabaseLogAppModel', 'DatabaseLog.Model');
App::uses('Hash', 'Utility');

class Log extends DatabaseLogAppModel {

	public $displayField = 'type';

	public $searchFields = array('Log.type');

	public function beforeSave($options = array()) {
		$this->data[$this->alias]['ip'] = env('REMOTE_ADDR');
		$this->data[$this->alias]['hostname'] = env('HTTP_HOST');
		$this->data[$this->alias]['uri'] = env('REQUEST_URI');
		$this->data[$this->alias]['refer'] = env('HTTP_REFERER');
		$this->data[$this->alias]['user_agent'] = env('HTTP_USER_AGENT');

		return parent::beforeSave($options);
	}

	/**
	* Return a text search on message
	*
	* @param string $query search string or `type@...`
	* @return array Conditions
	*/
	public function textSearch($query = null) {
		if ($query) {
			if (strpos($query, 'type@') === 0) {
				$query = str_replace('type@', '', $query);
				return array('Log.type' => $query);
			} else {
				$escapedQuery = $this->getDataSource()->value($query);
				return array("MATCH ({$this->alias}.message) AGAINST ($escapedQuery)");
			}
		}
		return array();
	}

	/**
	* Return all the unique types
	*
	* @return array Types
	*/
	public function getTypes() {
		$cache_key = 'database_log_types';
		if ($retval = Cache::read($cache_key)) {
			return $retval;
		}
		$retval = $this->find('all', array(
			'fields' => array('DISTINCT Log.type'),
			'order' => array('Log.type ASC')
		));
		$retval = Hash::extract($retval,'{n}.Log.type');
		Cache::write($cache_key, $retval);
		return $retval;
	}

	/**
	 * Remove duplicates and leave only the newest entry
	 * Also stores the new total "number" of this message in the remaining one
	 *
	 * @return void
	 */
	public function removeDuplicates() {
		$this->virtualFields['count'] = 'COUNT(*)';
		$options = array(
			'fields' => array('id', 'type', 'message', 'count'),
			'conditions' => array(),
			'group' => array('type', 'message'),
			//'having' => $this->alias . '__count > 0',
			'order' => array('created' => 'DESC')
		);
		$logs = $this->find('all', $options);

		foreach ($logs as $key => $log) {
			if ($log['Log']['count'] <= 1) {
				continue;
			}
			$options = array(
				'fields' => array('id'),
				'conditions' => array(
					'type' => $log['Log']['type'],
					'message' => $log['Log']['message'],
				),
				'order' => array('created' => 'DESC')
			);
			$entries = $this->find('list', $options);

			// keep the newest entry
			$keep = array_shift($entries);
			$this->deleteAll(array('id' => $entries));
			$this->updateAll(array('number = number + ' . count($entries)), array('id' => $keep));
		}
	}

}
