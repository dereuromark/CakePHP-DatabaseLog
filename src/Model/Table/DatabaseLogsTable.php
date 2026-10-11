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
use Cake\Database\Expression\QueryExpression;
use Cake\Datasource\EntityInterface;
use Cake\Event\Event;
use Cake\Event\EventInterface;
use Cake\I18n\DateTime;
use Cake\Routing\Router;
use Cake\Utility\Hash;
use Cake\Utility\Text;
use DatabaseLog\Model\Entity\DatabaseLog;
use DatabaseLog\Model\Filter\DatabaseLogsCollection;
use DateInterval;

/**
 * @method \DatabaseLog\Model\Entity\DatabaseLog get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \DatabaseLog\Model\Entity\DatabaseLog newEntity(array $data, array $options = [])
 * @method array<\DatabaseLog\Model\Entity\DatabaseLog> newEntities(array $data, array $options = [])
 * @method \DatabaseLog\Model\Entity\DatabaseLog|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \DatabaseLog\Model\Entity\DatabaseLog patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\DatabaseLog\Model\Entity\DatabaseLog> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \DatabaseLog\Model\Entity\DatabaseLog findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \DatabaseLog\Model\Entity\DatabaseLog saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \DatabaseLog\Model\Entity\DatabaseLog newEmptyEntity()
 * @method \Cake\Datasource\ResultSetInterface<\DatabaseLog\Model\Entity\DatabaseLog>|false saveMany(iterable $entities, array $options = [])
 * @method \Cake\Datasource\ResultSetInterface<\DatabaseLog\Model\Entity\DatabaseLog> saveManyOrFail(iterable $entities, array $options = [])
 * @method \Cake\Datasource\ResultSetInterface<\DatabaseLog\Model\Entity\DatabaseLog>|false deleteMany(iterable $entities, array $options = [])
 * @method \Cake\Datasource\ResultSetInterface<\DatabaseLog\Model\Entity\DatabaseLog> deleteManyOrFail(iterable $entities, array $options = [])
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 * @mixin \Search\Model\Behavior\SearchBehavior
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
	 * @param array<string, mixed> $config Config data.
	 * @return void
	 */
	public function initialize(array $config): void {
		$this->setDisplayField('type');
		$this->addBehavior('Timestamp', ['modified' => false]);
		if (static::isSearchEnabled()) {
			$this->addBehavior('Search.Search', [
				'collectionClass' => DatabaseLogsCollection::class,
			]);
		}

		if ($this->isAutoTableEnabled()) {
			$this->ensureTables(['DatabaseLog.DatabaseLogs']);
		}

		$callback = Configure::read('DatabaseLog.monitorCallback');
		if (!$callback) {
			return;
		}
		$this->getEventManager()->on('DatabaseLog.alert', $callback);
	}

	/**
	 * Whether the logs table may be created lazily at runtime.
	 *
	 * Defaults to `debug` mode only. Auto-creation issues a `listTables()` call on every
	 * instantiation of this table, and that goes through the connection's schema cache. If
	 * the cache engine backing `_cake_model_` is unreachable, the engine logs its own
	 * connection failure, which builds this log engine, which instantiates this table, which
	 * asks for the schema cache again - an unbounded recursion that only ends when the
	 * process runs out of memory. Restricting auto-creation to debug mode keeps that path out
	 * of production entirely.
	 *
	 * `DatabaseLog.disableAutoTable` still wins when set explicitly: `true` forces it off,
	 * `false` forces it on regardless of debug mode.
	 *
	 * @return bool
	 */
	protected function isAutoTableEnabled(): bool {
		$disabled = Configure::read('DatabaseLog.disableAutoTable');
		if ($disabled !== null) {
			return !$disabled;
		}

		return (bool)Configure::read('debug');
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
		$context = json_encode($context, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) ?: '';

		if (mb_strlen($message) > 65535) {
			$message = mb_substr($message, 0, 65535);
		}
		if (mb_strlen($context) > 65535) {
			$context = mb_substr($context, 0, 65535);
		}

		$data = [
			'type' => $level,
			'summary' => $summary,
			'message' => $message,
			'context' => $context,
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
	public function beforeSave(EventInterface $event, EntityInterface $entity, ArrayObject $options): void {
		$entity->ip = Router::getRequest()?->clientIp() ?: null;
		$entity->hostname = (string)env('HTTP_HOST') ?: gethostname() ?: null;
		$entity->uri = (string)env('REQUEST_URI') ?: null;
		$entity->refer = (string)env('HTTP_REFERER') ?: null;
		$entity->user_agent = (string)env('HTTP_USER_AGENT') ?: null;

		if (PHP_SAPI === 'cli') {
			if (!$entity->hostname) {
				$entity->hostname = (string)env('SERVER_NAME') ?: null;
			}
			if (!$entity->hostname) {
				$user = env('USER');
				$logName = env('LOGNAME');
				if ($user || $logName) {
					$entity->hostname = $user . '@' . $logName;
				}
			}
			if (!$entity->uri) {
				$type = 'CLI';
				$entity->uri = $type . ' ' . str_replace((string)env('PWD'), '', implode(' ', (array)($_SERVER['argv'] ?? null)));
			}
			if (!$entity->user_agent) {
				$shell = env('SHELL') ?: 'n/a';
				$entity->user_agent = $shell . ' (' . php_uname() . ')';
			}
		}

		$env = getenv('APPLICATION_ENV');
		if ($env) {
			$entity->user_agent .= ($entity->user_agent ? '' : 'n/a') . ' [' . $env . ']';
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
			if (str_starts_with($query, 'type@')) {
				$query = str_replace('type@', '', $query);

				return [$this->aliasField('type') => $query];
			}

			// Use proper escaping for fulltext search
			$connection = $this->getConnection();
			$escapedQuery = $connection->getDriver()->quote($query);

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
		// Use GROUP BY rather than DISTINCT here — combining DISTINCT with an
		// aggregate like COUNT(*) is rejected by Postgres in strict SQL mode
		// ("column must appear in the GROUP BY clause") even though SQLite
		// and MySQL silently accept it.
		$query = $this->find();
		$types = $query
			->select(['type', 'count' => $query->func()->count('*')])
			->groupBy('type')
			->orderByAsc('type')
			->disableHydration()
			->toArray();

		return Hash::combine($types, '{n}.type', '{n}.count');
	}

	/**
	 * Get log statistics grouped by time period.
	 *
	 * @param string $period '24h', '7d', or '30d'
	 * @return array{labels: array<string>, datasets: array<string, array<string, int>>}
	 */
	public function getStatsByPeriod(string $period = '24h'): array {
		$now = new DateTime();
		$connection = $this->getConnection();
		$driver = $connection->getDriver();
		$driverClass = get_class($driver);

		// Determine grouping and date range based on period
		switch ($period) {
			case '7d':
				$start = $now->modify('-7 days')->startOfDay();
				$format = 'Y-m-d';
				$labelFormat = 'M j';
				$interval = 'P1D';

				break;
			case '30d':
				$start = $now->modify('-30 days')->startOfDay();
				$format = 'Y-m-d';
				$labelFormat = 'M j';
				$interval = 'P1D';

				break;
			case '24h':
			default:
				$start = $now->modify('-24 hours');
				$format = 'Y-m-d H';
				$labelFormat = 'H:00';
				$interval = 'PT1H';

				break;
		}

		// Generate all time slots for the period
		$slots = [];
		$labels = [];
		$current = clone $start;
		$end = new DateTime();
		while ($current <= $end) {
			$key = $current->format($format);
			$slots[$key] = 0;
			$labels[$key] = $current->format($labelFormat);
			$current = $current->add(new DateInterval($interval));
		}

		// Build date expression based on database driver
		if (str_contains($driverClass, 'Sqlite')) {
			$dateExpr = $period === '24h' ? "strftime('%Y-%m-%d %H', created)" : "strftime('%Y-%m-%d', created)";
		} elseif (str_contains($driverClass, 'Postgres')) {
			$dateExpr = $period === '24h' ? "to_char(created, 'YYYY-MM-DD HH24')" : "to_char(created, 'YYYY-MM-DD')";
		} elseif ($period === '24h') {
			// MySQL
			$dateExpr = "DATE_FORMAT(created, '%Y-%m-%d %H')";
		} else {
			$dateExpr = "DATE_FORMAT(created, '%Y-%m-%d')";
		}

		// Query grouped data
		$query = $this->find();
		$periodExpr = $query->expr($dateExpr);

		$results = $query
			->select([
				'period' => $periodExpr,
				'type',
				'count' => $query->func()->count('*'),
			])
			->where(['created >=' => $start])
			->groupBy([$periodExpr, 'type'])
			->disableHydration()
			->all()
			->toArray();

		// Organize by type
		$data = [];
		foreach ($results as $row) {
			$type = $row['type'];
			if (!isset($data[$type])) {
				$data[$type] = $slots;
			}
			if (isset($data[$type][$row['period']])) {
				$data[$type][$row['period']] = (int)$row['count'];
			}
		}

		return [
			'labels' => array_values($labels),
			'datasets' => $data,
		];
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
			->orderByAsc('type')
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
		$logs = $query->select(['type', $field, 'count' => $query->func()->count('*')])
			->groupBy(['type', $field])
			->disableHydration()
			->toArray();

		$count = 0;
		foreach ($logs as $log) {
			if ($log['count'] <= 1) {
				continue;
			}
			$entries = $this->find('list', keyField: 'id', valueField: 'count')
				->where(['type' => $log['type'], $field => $log[$field]])
				->orderByDesc('created')
				->orderByDesc('id')
				->toArray();

			$keep = array_key_first($entries);
			if ($keep === null || count($entries) < 2) {
				continue;
			}
			unset($entries[$keep]);
			$count += $this->getConnection()->transactional(function () use ($entries, $keep): int {
				$this->deleteAll(['id IN' => array_keys($entries)]);

				return $this->updateAll(
					[
						'count' => new QueryExpression(
							'CASE WHEN count < 1 THEN 1 ELSE count END + '
							. array_sum(array_map(static fn (int|string $occurrences): int => max(1, (int)$occurrences), $entries)),
						),
					],
					['id' => $keep],
				);
			});
		}

		return $count;
	}

	/**
	 * @return int
	 */
	public function garbageCollector() {
		$deleted = $this->_cleanByAge();

		$query = $this->find()
			->orderByAsc('id');

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
	protected function _cleanByAge(): int {
		$deleted = 0;

		// Per-type retention policies
		$retention = Configure::read('DatabaseLog.retention');
		if ($retention && is_array($retention)) {
			foreach ($retention as $type => $age) {
				if (!$age) {
					continue;
				}
				$date = new DateTime($age);
				$deleted += $this->deleteAll([
					'type' => $type,
					'created <' => $date,
				]);
			}
		}

		// Global maxLength (fallback for types not in retention config)
		$age = Configure::read('DatabaseLog.maxLength');
		if (!$age) {
			return $deleted;
		}

		$date = new DateTime($age);
		$conditions = ['created <' => $date];

		// Exclude types that have their own retention policy
		if ($retention && is_array($retention)) {
			$conditions['type NOT IN'] = array_keys($retention);
		}

		return $deleted + $this->deleteAll($conditions);
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
	 * @param array<\DatabaseLog\Model\Entity\DatabaseLog> $logs
	 * @return void
	 */
	public function notify(array $logs): void {
		$event = new Event('DatabaseLog.alert', $this, ['logs' => $logs]);
		$this->getEventManager()->dispatch($event);
	}

	/**
	 * @param \DatabaseLog\Model\Entity\DatabaseLog $log
	 * @return string
	 */
	public function format(DatabaseLog $log): string {
		$content = $log->created . ': ' . $log->type;
		if ($log->ip) {
			$content .= ' - IP: ' . $log->ip;
		}
		if ($log->refer) {
			$content .= ' - Referer: ' . $log->refer;
		}

		return $content . (PHP_EOL . $log->message . PHP_EOL);
	}

	/**
	 * @return string SQL DB type
	 */
	public function databaseType(): string {
		$config = $this->getConnection()->config();
		$type = $config['driver'];

		return substr((string)$type, strrpos((string)$type, '\\') + 1);
	}

	/**
	 * Get size in bytes of the logs table.
	 *
	 * Supports SQLite (file size), MySQL, and PostgreSQL.
	 *
	 * @return int|null Bytes (null if database type not supported or cannot determine size)
	 */
	public function databaseSize(): ?int {
		$dbType = $this->databaseType();
		$config = $this->getConnection()->config();
		$tableName = $this->getTable();

		if ($dbType === 'Sqlite') {
			// SQLite doesn't support per-table size queries, return total file size
			if (!$config['database'] || $config['database'] === ':memory:') {
				return null;
			}

			$size = filesize($config['database']);

			return $size !== false ? $size : null;
		}

		if ($dbType === 'Mysql') {
			$database = $config['database'] ?? null;
			if (!$database) {
				return null;
			}

			$result = $this->getConnection()->execute(
				'SELECT SUM(data_length + index_length) as size FROM information_schema.TABLES WHERE table_schema = ? AND table_name = ?',
				[$database, $tableName],
			)->fetch('assoc');

			return $result['size'] ? (int)$result['size'] : null;
		}

		if ($dbType === 'Postgres') {
			$result = $this->getConnection()->execute(
				'SELECT pg_total_relation_size(?) as size',
				[$tableName],
			)->fetch('assoc');

			return $result['size'] ? (int)$result['size'] : null;
		}

		// Unsupported database type
		return null;
	}

}
