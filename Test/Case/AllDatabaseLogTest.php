<?php

/**
 * All Plugin tests
 *
 * This test suite will run all tests.
 */
class AllPluginTest extends PHPUnit_Framework_TestSuite {

	/**
	 * Defines tests for this suite
	 *
	 * @return CakeTestSuite The test suite.
	 */
	public static function suite() {
		$suite = new CakeTestSuite('All DatabaseLog tests');

		$path = dirname(__FILE__) . DS;
		$suite->addTestDirectory($path . 'Lib' . DS . 'Log' . DS . 'Engine');
		$suite->addTestDirectory($path . 'Model');
		$suite->addTestDirectory($path . 'Controller');

		return $suite;
	}
}
