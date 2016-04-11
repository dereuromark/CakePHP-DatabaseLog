<?php
/**
 * CakePHP DatabaseLog Plugin
 *
 * Licensed under The MIT License.
 *
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 * @link https://github.com/dereuromark/CakePHP-DatabaseLog
 */
namespace DatabaseLog\Model\Table;

use ArrayObject;
use Cake\Cache\Cache;
use Cake\Datasource\EntityInterface;
use Cake\Event\Event;
use Cake\Utility\Hash;

class LogsTable extends DatabaseLogAppTable {

	public $displayField = 'type';

	public $searchFields = array('Log.type');

	public function beforeSave(Event $event, EntityInterface $entity, ArrayObject $options) {
		$entity['ip'] = env('REMOTE_ADDR');
		$entity['hostname'] = env('HTTP_HOST');
		$entity['uri'] = env('REQUEST_URI');
		$entity['refer'] = env('HTTP_REFERER');
		$entity['user_agent'] = env('HTTP_USER_AGENT');
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
			}

			$escapedQuery = "'" . $query . "'"; // for now - $this->getDataSource()->value($query);
			return array("MATCH ({$this->alias()}.message) AGAINST ($escapedQuery)");
		}
		return array();
	}

	/**
	* Return all the unique types
	*
	* @return array Types
	*/
	public function getTypes() {
		$cacheKey = 'database_log_types';
		$retval = Cache::read($cacheKey);
		if ($retval) {
			return $retval;
		}

		$types = $this->find()->distinct('Logs.type')->select(['type'])->order('Logs.type ASC')->toArray();

		$retval = Hash::extract($types, '{n}.type');

		Cache::write($cacheKey, $retval);
		return $retval;
	}

	/**
	 * Remove duplicates and leave only the newest entry
	 * Also stores the new total "number" of this message in the remaining one
	 *
	 * @return void
	 */
	public function removeDuplicates() {
		$query = $this->find();
		$options = array(
			'fields' => array('id', 'type', 'message', 'count' => $query->func()->count('*')),
			'conditions' => array(),
			'group' => array('type', 'message'),
			//'having' => $this->alias . '__count > 0',
			'order' => array('created' => 'DESC')
		);
		$logs = $query->find('all', $options);

		foreach ($logs as $key => $log) {
			if ($log['count'] <= 1) {
				continue;
			}
			$options = array(
				'fields' => array('id'),
				'conditions' => array(
					'type' => $log['type'],
					'message' => $log['message'],
				),
				'order' => array('created' => 'DESC')
			);
			$entries = $this->find('list', $options)->toArray();

			// keep the newest entry
			$keep = array_shift($entries);
			if (!empty($entries)) {
				$this->deleteAll(array('id IN' => $entries));
			}
			$this->updateAll(array('number = number + ' . count($entries)), array('id' => $keep));
		}
	}

}
