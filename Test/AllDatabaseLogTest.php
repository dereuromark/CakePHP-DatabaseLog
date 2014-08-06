<?php
/**
 * AllTests file
 */

/**
 * AllTests class
 *
 * This test group will run all tests.
 */
class AllTests extends PHPUnit_Framework_TestSuite {

	/**
	 * Defines tests for this suite
	 *
	 * @return PHPUnit_Framework_TestSuite
	 */
	public static function suite() {
		$suite = new CakeTestSuite('All DatabaseLog tests');

		$path = dirname(__FILE__) . DS;
		$suite->addTestDirectory($path . 'Controller');
		$suite->addTestDirectory($path . 'Lib' . DS . 'Log' . DS . 'Engine');
		$suite->addTestDirectory($path . 'Model');

		return $suite;
	}
}
