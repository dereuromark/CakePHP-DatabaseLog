<?php
/* Log Test cases generated on: 2011-08-08 13:46:32 : 1312832792*/
App::import('Model', 'database_logger.Log');

class LogTestCase extends CakeTestCase {
	function startTest() {
		$this->Log =& ClassRegistry::init('Log');
	}

	function endTest() {
		unset($this->Log);
		ClassRegistry::flush();
	}

}
