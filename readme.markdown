# Database CakePHP Plugin
* Author: Nick Baker
* Version: 1.1
* License: MIT
* Website: <http://www.webtechnick.com>

## Features

Database CakeLogger for CakePHP 1.3 applications.  Easy setup.  Ideal for multi app applications where logging to a file
is just not convinient.  Simple admin interface to view/delete logs included.

## Changelog
* 1.1.0 Adding new fields URI, hostname, referrer, and IP automatically logged on each log call. (only applys to default Log model)
* 1.0.0 Initial Release

## Install

Clone the repository into your `app/plugins/database_logger` directory:

	$ git clone git://github.com/webtechnick/CakePHP-DatabaseLogger-Plugin.git app/plugins/database_logger

Run the schema into your database:

	$ cake schema create database_logger -plugin database_logger
	
## Setup

Update the file `app/config/bootstrap.php` with the following configurations like so:

		CakeLog::config('database', array(
			'engine' => 'DatabaseLogger.DatabaseLogger',
		));

## Usage

Anywhere in your app where you call log() or CakeLog::write the database logger will be used.

		$this->log('This is a detailed message logged to the database','error');
		CakeLog::write('error', 'This is a detailed message logged to the database');
		
Navigate to `http://www.example.com/admin/database_logger/logs` to view/search/delete your logs.