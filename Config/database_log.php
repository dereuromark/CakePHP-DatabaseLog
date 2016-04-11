<?php
/**
 * CakePHP DatabaseLog Plugin
 *
 * Licensed under The MIT License.
 *
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 * @link https://github.com/dereuromark/CakePHP-DatabaseLog
 */namespace DatabaseLog\Config;



/**
 * DatabaseLog (Default) Configuration
 *
 * Copy this file to app/Config/database_log.php
 * and customize it to your needs.
 *
 * @see DatabaseLogAppModel::__construct()
 */
$config = array(
	'DatabaseLog' => array(
		'write' => 'default', // DataSource to write to.
		'read' => 'default', // DataSource to read from.
	)
);
