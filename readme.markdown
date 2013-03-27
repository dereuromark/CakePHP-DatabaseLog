# Database CakePHP Plugin
* Author: Nick Baker
* Version: 1.3
* License: MIT
* Website: <http://www.webtechnick.com>

## Features

Database CakeLogger for CakePHP 2.x applications.  Easy setup.  Ideal for multi app applications where logging to a file
is just not convinient.  Simple admin interface to view/delete logs included.

## Changelog
* 1.3.0 New configuration file to change default read, write datasources.
* 1.2.0 Now using FULLTEXT search on messages, better indexes.  Update your schema.
* 1.1.0 Adding new fields URI, hostname, referrer, and IP automatically logged on each log call. (only applys to default Log model)
* 1.0.0 Initial Release

## Install

Clone the repository into your `app/Plugin/DatabaseLogger` directory:

	$ git clone git://github.com/webtechnick/CakePHP-DatabaseLogger-Plugin.git app/Plugin/DatabaseLogger

Run the schema into your database:

	$ cake schema create --plugin DatabaseLogger
	
## Setup

Create a config file in `app/Config/database_logger` with the following (example file in plugin.)

	$config = array(
		'DatabaseLogger' => array(
			'write' => 'default', //DataSource to write to.
			'read' => 'default', //Datasource to read from.
		)
	);
	
Pro Tip: You can read from a different datasource than you write to, and they both can be different than your default.

Update the file `app/Config/bootstrap.php` with the following configurations like so:

	App::uses('CakeLog','Log');
	CakeLog::config('default', array('engine' => 'DatabaseLogger.DatabaseLogger'));

## Usage

Anywhere in your app where you call log() or CakeLog::write the database logger will be used.

		$this->log('This is a detailed message logged to the database','error');
		CakeLog::write('error', 'This is a detailed message logged to the database');
		
Navigate to `http://www.example.com/admin/database_logger/logs` to view/search/delete your logs.