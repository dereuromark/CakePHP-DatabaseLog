<?php
/**
 * group test - DatabaseLog
 */
class AllPluginTest extends PHPUnit_Framework_TestSuite {

	/**
	 * suite method, defines tests for this suite.
	 *
	 * @return void
	 */
	public static function suite() {
		$Suite = new CakeTestSuite('All Plugin tests');
		$path = dirname(__FILE__);
		$Suite->addTestDirectory($path . DS . 'Lib' . DS . 'Log' . DS . 'Engine');
		$Suite->addTestDirectory($path . DS . 'Model');
		$Suite->addTestDirectory($path . DS . 'Controller');
		return $Suite;
	}
}
