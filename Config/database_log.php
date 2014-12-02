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
 * DatabaseLog (Default) Configuration
 *
 * Either load the default config by calling
 * ``Configure::load('DatabaseLog.database_log', 'default')``
 * or copy this file to app/Config/database_log.php
 * and load the customizable config by calling
 * ``Configure::load('database_log', 'default')``
 */
$config = array(
	'DatabaseLog' => array(
		'write' => 'default', // DataSource to write to.
		'read' => 'default', // DataSource to read from.
	)
);
