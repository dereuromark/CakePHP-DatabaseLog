#  CakePHP DatabaseLog Plugin

[![CI](https://github.com/dereuromark/CakePHP-DatabaseLog/workflows/CI/badge.svg?branch=master)](https://github.com/dereuromark/CakePHP-DatabaseLog/actions?query=workflow%3ACI+branch%3Amaster)
[![Coverage Status](https://img.shields.io/codecov/c/github/dereuromark/CakePHP-DatabaseLog/master.svg)](https://codecov.io/github/dereuromark/CakePHP-DatabaseLog/branch/master)
[![Latest Stable Version](https://poser.pugx.org/dereuromark/CakePHP-DatabaseLog/v/stable.svg)](https://packagist.org/packages/dereuromark/CakePHP-DatabaseLog)
[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%207.3-8892BF.svg)](https://php.net/)
[![License](https://poser.pugx.org/dereuromark/CakePHP-DatabaseLog/license.png)](https://packagist.org/packages/dereuromark/CakePHP-DatabaseLog)
[![Total Downloads](https://poser.pugx.org/dereuromark/CakePHP-DatabaseLog/d/total.png)](https://packagist.org/packages/dereuromark/CakePHP-DatabaseLog)

DatabaseLog engine for CakePHP applications.

This branch is for **CakePHP 4.2+**. See [version map](https://github.com/dereuromark/CakePHP-DatabaseLog/wiki#cakephp-version-map) for details.

## Features

- Easy setup and almost no dependencies.
- Detailed log infos added for both Web and CLI log entries.
- Defaults to SQLite as single app application lightweight approach.
- Ideal for multi-server or serverless applications where logging to a file is just not convenient.
- If DB is used, fallback to SQLite in case the DB is not reachable.
- Simple admin interface to view/delete logs included.
- Basic monitoring and alert system included.
- Export to TXT files possible.

### Log Rotation
While file handling requires file log rotation and splitting into chunks of (compressed) files, a database approach can more easily keep the logs together in a single database. This is more convinient when looking through them or searching for something specific.

This plugin internally combines log entries of the exact same "content" into a single row with an increased count.
Additionally you would want to add a cronjob triggered cleanup shell to keep the total size and row count below a certain threshold.

## Demo
Clone and install the [sandbox app](https://github.com/dereuromark/cakephp-sandbox), create some errors and browse the admin backend for the logs overview.

Or just attach it to your app directly. Time needed: 5 minutes.

## Install

### Composer (preferred)
```
composer require dereuromark/cakephp-databaselog
```

## Setup
Enable the plugin in your `Application` class:
 ```php
$this->addPlugin('DatabaseLog');
 ```
or just call:
```
bin/cake plugin load DatabaseLog
```

You can simply modify the existing config entries in your `config/app.php`:
 ```php
	'Log' => [
		'debug' => [
			'className' => 'DatabaseLog.Database',
		],
		'error' => [
			'className' => 'DatabaseLog.Database',
		],
		...
	],
```
This will use the `database_log` connection and an SQLite file database by default, stored in your `logs` folder.

### Using an actual database (optional)
Create a config setting in your `config/app.php` what database connection it should log to:
```php
'DatabaseLog' => [
	'datasource' => 'my_datasource', // DataSource to use
]
```
It is recommended to not use the same datasource as your production server (`default`) because when the DB is not reachable logging to it will
also not be possible. In that case it will fall back to SQLite file logging on this server instance, though.

Once the datasource is reachable and once the first log entry is being written, the database table (defaulting to `database_logs`) will be automatically
created. No need to manually run any migration or SQL script here.
You can also manually create the table beforehand, if you prefer:
```
bin/cake Migrations migrate -p DatabaseLog
```
Or just copy the migration file into your app `/config/Migrations`, modify if needed, and then run it as part of your app migrations.

Fully tested so far are PostgreSQL and MySQL, but by using the ORM all major databases should be supported.

## Usage

Anywhere in your app where you call `$this->log()` or `Log::write()` the DatabaseLog engine will be used.
```php
$this->log('This is a detailed message logged to the database', 'error');
// or
Log::write('error', 'This is a detailed message logged to the database');
```
There is also a browsable web backend you can view your logs with.

See [Docs](/docs) for more details.
