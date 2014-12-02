<?php

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
