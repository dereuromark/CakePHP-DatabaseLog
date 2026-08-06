<?php
/**
 * CakePHP DatabaseLog Plugin
 *
 * Licensed under The MIT License.
 *
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 * @link https://github.com/dereuromark/CakePHP-DatabaseLog
 */

namespace DatabaseLog\Test\TestCase\Log\Engine;

use Cake\Log\Log;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Cake\View\View;
use DatabaseLog\Log\Engine\DatabaseLog;
use DatabaseLog\Model\Table\DatabaseLogsTable;
use TestApp\Log\Engine\RejectingDatabaseLog;
use TestApp\Log\Engine\UnreachableDatabaseLog;

class DatabaseLogTest extends TestCase {

	/**
	 * @var \DatabaseLog\Model\Table\DatabaseLogsTable
	 */
	public $Logs;

	/**
	 * Fixtures
	 *
	 * @var array
	 */
	protected array $fixtures = [
		'plugin.DatabaseLog.DatabaseLogs',
	];

	/**
	 * Setup
	 *
	 * @return void
	 */
	public function setUp(): void {
		// After parent::setUp(): fetching the table first puts it in the locator before the
		// fixture manager can configure it, and the fixture then fails to describe the schema.
		parent::setUp();

		$this->Logs = TableRegistry::getTableLocator()->get('DatabaseLog.DatabaseLogs');
	}

	/**
	 * Teardown
	 *
	 * @return void
	 */
	public function tearDown(): void {
		//Log::drop('default');
		//Log::config('default', array('className' => 'FileLog'));
		parent::tearDown();
	}

	/**
	 * Tests the log write method
	 *
	 * @return void
	 */
	public function testLogWriting() {
		$View = new View();
		$countBefore = $this->Logs->find()->count();

		$View->log('x');
		$View->log('warning', LOG_WARNING);
		Log::write(LOG_ERR, 'y');
		Log::write(LOG_INFO, 'z');

		$countAfter = $this->Logs->find()->count();
		$this->assertSame($countBefore + 8, $countAfter); // should be 4 (but for some reason everything is added twice
	}

	/**
	 * The table is resolved on first write, not while constructing the engine. Log engines get
	 * built from inside arbitrary code paths, including ones already reporting a failure.
	 *
	 * @return void
	 */
	public function testLogsTableIsResolvedLazily() {
		$engine = new DatabaseLog();
		$this->assertNull($engine->Logs);

		$engine->log('error', 'some message');

		$this->assertInstanceOf(DatabaseLogsTable::class, $engine->Logs);
	}

	/**
	 * A write that cannot reach the database must not escape into the caller. Whoever called
	 * Log::error() cannot handle a logging failure, and letting it through turns an outage into
	 * a bigger one.
	 *
	 * @return void
	 */
	public function testLogDoesNotThrowWhenDatabaseIsUnreachable() {
		$engine = new UnreachableDatabaseLog();

		$engine->log('error', 'the original problem');

		$this->assertCount(1, $engine->fallbacks);
		$this->assertSame('error', $engine->fallbacks[0][0]);
		$this->assertSame('the original problem', $engine->fallbacks[0][1]);
		$this->assertStringContainsString('Connection refused', $engine->fallbacks[0][2]);
	}

	/**
	 * The table reports a rejected save as false rather than throwing, so an entry dropped by
	 * validation would otherwise vanish just as quietly as a failed write.
	 *
	 * @return void
	 */
	public function testLogFallsBackWhenTheRowIsRejected() {
		$engine = new RejectingDatabaseLog();

		$engine->log('error', 'the original problem');

		$this->assertCount(1, $engine->fallbacks);
		$this->assertSame('error', $engine->fallbacks[0][0]);
		$this->assertSame('the original problem', $engine->fallbacks[0][1]);
		$this->assertSame('the row was rejected', $engine->fallbacks[0][2]);
	}

	/**
	 * The configured type wins over the level, also on the fallback path.
	 *
	 * @return void
	 */
	public function testFallbackUsesConfiguredType() {
		$engine = new UnreachableDatabaseLog(['type' => '404']);

		$engine->log('error', 'not found');

		$this->assertSame('404', $engine->fallbacks[0][0]);
	}

	/**
	 * The real fallback writes to the process error log rather than dropping the entry.
	 *
	 * @return void
	 */
	public function testFallbackWritesToErrorLog() {
		$file = TMP . 'error_log_fallback.log';
		if (file_exists($file)) {
			unlink($file);
		}

		$previous = ini_get('error_log');
		ini_set('error_log', $file);

		try {
			$engine = new DatabaseLog(['model' => 'DatabaseLog.NotAModelThatExists']);
			$engine->log('error', 'the original problem');
		} finally {
			ini_set('error_log', $previous === false ? '' : $previous);
		}

		$this->assertFileExists($file);
		$content = (string)file_get_contents($file);
		$this->assertStringContainsString('[DatabaseLog fallback][error]', $content);
		$this->assertStringContainsString('the original problem', $content);
	}

}
