<?php
/**
 * CakePHP DatabaseLog Plugin
 *
 * Licensed under The MIT License.
 *
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 * @link https://github.com/dereuromark/CakePHP-DatabaseLog
 */

/**
 * All controller tests
 *
 * This test suite will run all controller tests.
 */
class AllControllerTest extends PHPUnit_Framework_TestSuite {

	/**
	 * Defines tests for this suite
	 *
	 * @return CakeTestSuite The test suite.
	 */
	public static function suite() {
		$Suite = new CakeTestSuite('All Controller tests');
		$path = dirname(__FILE__);
		$Suite->addTestDirectory($path . DS . 'Controller');
		return $Suite;
	}
}
