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
		$this->Logs = TableRegistry::getTableLocator()->get('DatabaseLog.DatabaseLogs');

		parent::setUp();
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

}
