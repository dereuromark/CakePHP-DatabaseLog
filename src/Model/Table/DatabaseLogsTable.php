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
use Cake\Datasource\EntityInterface;
use Cake\Datasource\ResultSetInterface;
use Cake\Event\Event;
use Cake\Event\EventInterface;
use Cake\I18n\FrozenTime;
use Cake\Utility\Hash;
use Cake\Utility\Text;
use DatabaseLog\Model\Entity\DatabaseLog;
use RuntimeException;

/**
 * @method \DatabaseLog\Model\Entity\DatabaseLog get($primaryKey, $options = [])
 * @method \DatabaseLog\Model\Entity\DatabaseLog newEntity(array $data, array $options = [])
 * @method \DatabaseLog\Model\Entity\DatabaseLog[] newEntities(array $data, array $options = [])
 * @method \DatabaseLog\Model\Entity\DatabaseLog|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \DatabaseLog\Model\Entity\DatabaseLog patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \DatabaseLog\Model\Entity\DatabaseLog[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \DatabaseLog\Model\Entity\DatabaseLog findOrCreate($search, ?callable $callback = null, $options = [])
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 * @method \DatabaseLog\Model\Entity\DatabaseLog saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @mixin \Search\Model\Behavior\SearchBehavior
 * @method \DatabaseLog\Model\Entity\DatabaseLog newEmptyEntity()
 * @method \DatabaseLog\Model\Entity\DatabaseLog[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \DatabaseLog\Model\Entity\DatabaseLog[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \DatabaseLog\Model\Entity\DatabaseLog[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \DatabaseLog\Model\Entity\DatabaseLog[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class DatabaseLogsTable extends DatabaseLogAppTable {

	use LazyTableTrait;

	/**
	 * @var array<string>
	 */
	public $searchFields = ['DatabaseLogs.type'];

	/**
	 * initialize method
	 *
	 * @param array $config Config data.
	 * @return void
	 */
	public function initialize(array $config): void {
		$this->setDisplayField('type');
		$this->addBehavior('Timestamp', ['modified' => false]);
		if (static::isSearchEnabled()) {
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
			->like('search', ['fields' => ['summary'], 'before' => true, 'after' => true]);
			/*
			->callback('search', ['callback' => function (Query $query, array $args, Base $filter) {
				...
		 	}])
			*/

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
		$message = trim($message);
		$summary = Text::truncate($message, 255);

		$data = [
			'type' => $level,
			'summary' => $summary,
			'message' => $message,
			'context' => trim(print_r($context, true)),
			'count' => 1,
		];
		$log = $this->newEntity($data);

		return (bool)$this->save($log);
	}

	/**
	 * @param \Cake\Event\EventInterface $event
	 * @param \DatabaseLog\Model\Entity\DatabaseLog $entity
	 * @param \ArrayObject $options
	 * @return void
	 */
	public function beforeSave(EventInterface $event, EntityInterface $entity, ArrayObject $options) {
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
				$entity['uri'] = $type . ' ' . str_replace((string)env('PWD'), '', implode(' ', (array)env('argv')));
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
	public function getTypesWithCount() {
		$query = $this->find();
		$types = $query
			->select(['type', 'count' => 'COUNT(*)'])
			->distinct('type')
			->order('type ASC')
			->disableHydration()
			->toArray();

		return Hash::combine($types, '{n}.type', '{n}.count');
	}

	/**
	 * Return all the unique types
	 *
	 * @return array Types
	 */
	public function getTypes() {
		$types = $this->find()
			->select(['type'])
			->distinct('type')
			->order('type ASC')
			->disableHydration()
			->toArray();

		return Hash::combine($types, '{n}.type', '{n}.type');
	}

	/**
	 * Remove duplicates and leave only the newest entry
	 * Also stores the new total "number" of this message in the remaining one
	 *
	 * @param bool $strict
	 * @return int
	 */
	public function removeDuplicates($strict = false) {
		$field = $strict ? 'message' : 'summary';

		$query = $this->find();
		$options = [
			'fields' => ['type', $field, 'count' => $query->func()->count('*')],
			'conditions' => [],
			'group' => ['type', $field],
			//'having' => $this->alias . '__count > 0',
			//'order' => ['created' => 'DESC']
		];
		$logs = $query->find('all', $options)->disableHydration()->toArray();

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
					$field => $log[$field],
				],
				'order' => ['created' => 'DESC'],
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

		/** @var \DatabaseLog\Model\Entity\DatabaseLog|null $record */
		$record = $query->where()->offset($count - $limit)->first();
		if (!$record) {
			return $deleted;
		}

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

		$date = new FrozenTime($age);

		return $this->deleteAll(['created <' => $date]);
	}

	/**
	 * @return void
	 */
	public function truncate() {
		/** @var \Cake\Database\Schema\TableSchema $tableSchema */
		$tableSchema = $this->getSchema();
		$connection = $this->getConnection();
		$sql = $tableSchema->truncateSql($connection);
		foreach ($sql as $snippet) {
			$connection->execute($snippet);
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
		$content .= PHP_EOL . $log->message . PHP_EOL;

		return $content;
	}

	/**
	 * @return string SQL DB type
	 */
	public function databaseType(): string {
		$config = $this->getConnection()->config();
		$type = $config['driver'];

		return substr($type, strrpos($type, '\\') + 1);
	}

	/**
	 * @throws \RuntimeException
	 * @return int|null Bytes
	 */
	public function databaseSize(): ?int {
		if ($this->databaseType() !== 'Sqlite') {
			return null;
		}

		$config = $this->getConnection()->config();
		if (empty($config['database'])) {
			return null;
		}

		$size = filesize($config['database']);
		if ($size === false) {
			throw new RuntimeException('Cannot access DB ' . $config['database']);
		}

		return $size;
	}

}
