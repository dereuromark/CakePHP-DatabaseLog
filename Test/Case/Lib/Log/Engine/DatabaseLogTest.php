<?php
/**
 * CakePHP DatabaseLog Plugin
 *
 * Licensed under The MIT License.
 *
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 * @link https://github.com/dereuromark/CakePHP-DatabaseLog
 */

App::uses('DatabaseLog', 'Log/Engine');
App::uses('AppModel', 'Model');

/**
 * DatabaseLog Test
 *
 * @coversDefaultClass DatabaseLog
 */
class DatabaseLogTest extends CakeTestCase {

	/**
	 * Fixtures
	 *
	 * @var array
	 */
	public $fixtures = array(
		'plugin.database_log.log'
	);

	/**
	 * Setup
	 */
	public function setUp() {
		CakeLog::config('default', array('engine' => 'DatabaseLog.DatabaseLog'));
		$this->Log = ClassRegistry::init('DatabaseLog.Log');

		parent::setUp();
	}

	/**
	 * Teardown
	 */
	public function tearDown() {
		CakeLog::config('default', array('engine' => 'FileLog'));
		parent::tearDown();
	}

	/**
	 * Tests the log write method
	 *
	 * @return void
	 */
	public function testLogWriting() {
		$Model = ClassRegistry::init('TestLog');

		$countBefore = $this->Log->find('count');

		$Model->log('x');
		CakeLog::write('warning', 'y');
		CakeLog::write('info', 'z');

		$countAfter = $this->Log->find('count');
		$this->assertSame($countBefore + 3, $countAfter);
	}

}

/**
 * TestLog Model
 */
class TestLog extends AppModel {

	/**
	 * Use no table
	 *
	 * @var bool
	 */
	public $useTable = false;

}
