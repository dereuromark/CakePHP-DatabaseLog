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
use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\Datasource\EntityInterface;
use Cake\Datasource\ResultSetInterface;
use Cake\Event\Event;
use Cake\Utility\Hash;
use DatabaseLog\Model\Entity\DatabaseLog;

/**
 * @method \DatabaseLog\Model\Entity\DatabaseLog get($primaryKey, $options = [])
 * @method \DatabaseLog\Model\Entity\DatabaseLog newEntity($data = null, array $options = [])
 * @method \DatabaseLog\Model\Entity\DatabaseLog[] newEntities(array $data, array $options = [])
 * @method \DatabaseLog\Model\Entity\DatabaseLog|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \DatabaseLog\Model\Entity\DatabaseLog patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \DatabaseLog\Model\Entity\DatabaseLog[] patchEntities($entities, array $data, array $options = [])
 * @method \DatabaseLog\Model\Entity\DatabaseLog findOrCreate($search, callable $callback = null, $options = [])
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 * @method \DatabaseLog\Model\Entity\DatabaseLog|bool saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 */
class DatabaseLogsTable extends DatabaseLogAppTable {

	use LazyTableTrait;

	/**
	 * @var array
	 */
	public $searchFields = ['DatabaseLogs.type'];

	/**
	 * initialize method
	 *
	 * @param array $config Config data.
	 * @return void
	 */
	public function initialize(array $config) {
		$this->setDisplayField('type');
		$this->addBehavior('Timestamp', ['modified' => false]);
		if (Configure::read('DatabaseLog.isSearchEnabled') !== false && Plugin::isLoaded('DatabaseLog')) {
			$this->addBehavior('Search.Search');
		}
		$this->ensureTables(['DatabaseLog.DatabaseLogs']);

		$callback = Configure::read('DatabaseLog.monitorCallback');
		if (!$callback) {
			return;
		}
		$this->getEventManager()->on('DatabaseLog.alert', $callback);
	}

	/**
	 * @return \Search\Manager
	 */
	public function searchManager() {
		$searchManager = $this->behaviors()->Search->searchManager();
		$searchManager
			->value('type')
			->like('search', ['field' => ['message'], 'before' => true, 'after' => true]);

		return $searchManager;
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
			'message' => trim($message),
			'context' => trim(print_r($context, true)),
			'count' => 1
		];
		$log = $this->newEntity($data);

		return (bool)$this->save($log);
	}

	/**
	 * @param \Cake\Event\Event $event
	 * @param \DatabaseLog\Model\Entity\DatabaseLog $entity
	 * @param \ArrayObject $options
	 * @return void
	 */
	public function beforeSave(Event $event, EntityInterface $entity, ArrayObject $options) {
		$entity['ip'] = env('REMOTE_ADDR');
		$entity['hostname'] = env('HTTP_HOST') ?: gethostname();
		$entity['uri'] = env('REQUEST_URI');
		$entity['refer'] = env('HTTP_REFERER');
		$entity['user_agent'] = env('HTTP_USER_AGENT');

		if (PHP_SAPI === 'cli') {
			if (!$entity['hostname']) {
				$entity['hostname'] = env('SERVER_NAME');
			}
			if (!$entity['hostname']) {
				$user = env('USER');
				$logName = env('LOGNAME');
				if ($user || $logName) {
					$entity['hostname'] = $user . '@' . $logName;
				}
			}
			if (!$entity['uri']) {
				$type = 'CLI';
				$entity['uri'] = $type . ' ' . str_replace(env('PWD'), '', implode(' ', (array)env('argv')));
			}
			if (!$entity['user_agent']) {
				$shell = env('SHELL') ?: 'n/a';
				$entity['user_agent'] = $shell . ' (' . php_uname() . ')';
			}
		}

		$env = getenv('APPLICATION_ENV');
		if ($env) {
			$entity['user_agent'] .= ($entity['user_agent'] ? '' : 'n/a') . ' [' . $env . ']';
		}

		$callback = Configure::read('DatabaseLog.saveCallback');
		if (!is_callable($callback)) {
			return;
		}

		$callback($entity);
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
			return ["MATCH (message) AGAINST ($escapedQuery)"];
		}
		return [];
	}

	/**
	 * Return all the unique types
	 *
	 * @return array Types
	 */
	public function getTypes() {
		$types = $this->find()->select(['type'])->distinct('type')->order('type ASC')->toArray();
		return (array)Hash::combine($types, '{n}.type', '{n}.type');
	}

	/**
	 * Remove duplicates and leave only the newest entry
	 * Also stores the new total "number" of this message in the remaining one
	 *
	 * @return int
	 */
	public function removeDuplicates() {
		$query = $this->find();
		$options = [
			'fields' => ['type', 'message', 'count' => $query->func()->count('*')],
			'conditions' => [],
			'group' => ['type', 'message'],
			//'having' => $this->alias . '__count > 0',
			//'order' => ['created' => 'DESC']
		];
		$logs = $query->find('all', $options);

		$count = 0;
		foreach ($logs as $key => $log) {
			if ($log['count'] <= 1) {
				continue;
			}
			$options = [
				'fields' => ['id'],
				'keyField' => 'id',
				'valueField' => 'id',
				'conditions' => [
					'type' => $log['type'],
					'message' => $log['message'],
				],
				'order' => ['created' => 'DESC']
			];
			$entries = $this->find('list', $options)->toArray();

			// keep the newest entry
			$keep = array_shift($entries);
			if ($entries) {
				$this->deleteAll(['id IN' => $entries]);
			}
			$count += $this->updateAll(['count = count + ' . count($entries)], ['id' => $keep]);
		}

		return $count;
	}

	/**
	 * @return int
	 */
	public function garbageCollector() {
		$deleted = $this->_cleanByAge();

		$query = $this->find()
			->order(['id' => 'ASC']);

		$count = $query->count();

		$limit = Configure::read('DatabaseLog.limit') ?: 999999;
		if ($count <= $limit) {
			return $deleted;
		}

		$record = $query->where()->offset($count - $limit)->first();

		return $deleted + $this->deleteAll(['id <' => $record->id]);
	}

	/**
	 * @return int
	 */
	protected function _cleanByAge() {
		$age = Configure::read('DatabaseLog.maxLength');
		if (!$age) {
			return 0;
		}

		$date = strtotime($age);

		return $this->deleteAll(['created <' => $date]);
	}

	/**
	 * @return void
	 */
	public function truncate() {
		$sql = $this->getSchema()->truncateSql($this->_connection);
		foreach ($sql as $snippet) {
			$this->_connection->execute($snippet);
		}
	}

	/**
	 * @param \Cake\Datasource\ResultSetInterface $logs
	 * @return void
	 */
	public function notify(ResultSetInterface $logs) {
		$event = new Event('DatabaseLog.alert', $this, ['logs' => $logs]);
		$this->getEventManager()->dispatch($event);
	}

	/**
	 * @param \DatabaseLog\Model\Entity\DatabaseLog $log
	 * @return string
	 */
	public function format(DatabaseLog $log) {
		$content = $log->created . ': ' . $log->type;
		if ($log->ip) {
			$content .= ' - IP: ' . $log->ip;
		}
		if ($log->refer) {
			$content .= ' - Referer: ' . $log->refer;
		}
		$content .= PHP_EOL . $log->message;

		return $content;
	}

}
