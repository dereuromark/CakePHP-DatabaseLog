<?php

/**
 * All model tests
 *
 * This test suite will run all model tests.
 */
class AllModelTest extends PHPUnit_Framework_TestSuite {

	/**
	 * Defines tests for this suite
	 *
	 * @return CakeTestSuite The test suite.
	 */
	public static function suite() {
		$Suite = new CakeTestSuite('All Model tests');
		$path = dirname(__FILE__);
		$Suite->addTestDirectory($path . DS . 'Model');
		return $Suite;
	}
}
