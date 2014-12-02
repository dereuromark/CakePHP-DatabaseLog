<?php

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
