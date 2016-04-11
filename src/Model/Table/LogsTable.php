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

	use LazyTableTrait;

	/**
	 * @var array
	 */
	public $searchFields = ['Logs.type'];

	/**
	 * initialize method
	 *
	 * @param array $config Config data.
	 * @return void
	 */
	public function initialize(array $config) {
		$this->displayField('type');
		$this->addBehavior('Timestamp', ['modified' => false]);
		$this->ensureTables(['DatabaseLog.Logs']);
	}

	/**
	 * Write the log to database
	 *
	 * @param mixed $level
	 * @param string $message
	 * @param array $context
	 * @return bool Success
	 */
	public function log($level, $message, array $context = []) {
		$data = [
			'type' => $level,
			'message' => is_string($message) ? $message : print_r($message, true),
			'context' => is_string($context) ? $context : print_r($context, true),
		];
		$log = $this->newEntity($data);
		return (bool)$this->save($log);
	}

	/**
	 * @param \Cake\Event\Event $event
	 * @param \Cake\Datasource\EntityInterface $entity
	 * @param \ArrayObject $options
	 * @return void
	 */
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
	* @param string|null $query search string or `type@...`
	* @return array Conditions
	*/
	public function textSearch($query = null) {
		if ($query) {
			if (strpos($query, 'type@') === 0) {
				$query = str_replace('type@', '', $query);
				return ['Log.type' => $query];
			}

			$escapedQuery = "'" . $query . "'"; // for now - $this->getDataSource()->value($query);
			return ["MATCH ({$this->alias()}.message) AGAINST ($escapedQuery)"];
		}
		return [];
	}

	/**
	* Return all the unique types
	*
	* @return array Types
	*/
	public function getTypes() {
		$types = $this->find()->distinct('Logs.type')->select(['type'])->order('Logs.type ASC')->toArray();
		return Hash::extract($types, '{n}.type');
	}

	/**
	 * Remove duplicates and leave only the newest entry
	 * Also stores the new total "number" of this message in the remaining one
	 *
	 * @return void
	 */
	public function removeDuplicates() {
		$query = $this->find();
		$options = [
			'fields' => ['id', 'type', 'message', 'count' => $query->func()->count('*')],
			'conditions' => [],
			'group' => ['type', 'message'],
			//'having' => $this->alias . '__count > 0',
			'order' => ['created' => 'DESC']
		];
		$logs = $query->find('all', $options);

		foreach ($logs as $key => $log) {
			if ($log['count'] <= 1) {
				continue;
			}
			$options = [
				'fields' => ['id'],
				'conditions' => [
					'type' => $log['type'],
					'message' => $log['message'],
				],
				'order' => ['created' => 'DESC']
			];
			$entries = $this->find('list', $options)->toArray();

			// keep the newest entry
			$keep = array_shift($entries);
			if (!empty($entries)) {
				$this->deleteAll(['id IN' => $entries]);
			}
			$this->updateAll(['number = number + ' . count($entries)], ['id' => $keep]);
		}
	}

}
