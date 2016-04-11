<?php
/**
 * CakePHP DatabaseLog Plugin
 *
 * Licensed under The MIT License.
 *
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 * @link https://github.com/dereuromark/CakePHP-DatabaseLog
 */
namespace DatabaseLog\TestCase\Log\Engine;

use Cake\Log\Log;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;


use Cake\View\View;
use DatabaseLog\Log\Engine\DatabaseLog;

/**
 * DatabaseLog Test
 *
 * @coversDefaultClass DatabaseLog
 */
class DatabaseLogTest extends TestCase {

	/**
	 * @var \DatabaseLog\Model\Table\LogsTable
	 */
	public $Logs;

	/**
	 * Fixtures
	 *
	 * @var array
	 */
	public $fixtures = array(
		'plugin.database_log.logs'
	);

	/**
	 * Setup
	 */
	public function setUp() {
		Log::drop('default');
		Log::config('default', array('className' => 'DatabaseLog.Database'));
		$this->Logs = TableRegistry::get('DatabaseLog.Logs');

		parent::setUp();
	}

	/**
	 * Teardown
	 */
	public function tearDown() {
		Log::drop('default');
		Log::config('default', array('className' => 'FileLog'));
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
		Log::write('warning', 'y');
		Log::write('info', 'z');

		$countAfter = $this->Logs->find()->count();
		$this->assertSame($countBefore + 3, $countAfter);
	}

}
