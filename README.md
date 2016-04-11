#  CakePHP DatabaseLog Plugin

[![Build Status](https://api.travis-ci.org/dereuromark/CakePHP-DatabaseLog.png)](https://travis-ci.org/dereuromark/CakePHP-DatabaseLog)
[![License](https://poser.pugx.org/dereuromark/CakePHP-DatabaseLog/license.png)](https://packagist.org/packages/dereuromark/CakePHP-DatabaseLog)
[![Total Downloads](https://poser.pugx.org/dereuromark/CakePHP-DatabaseLog/d/total.png)](https://packagist.org/packages/dereuromark/CakePHP-DatabaseLog)

DatabaseLog engine for CakePHP applications.

**This branch is for CakePHP 3.x**

## Features

- Easy setup.
- Defaults to SQLite as single app application light weight approach.
- Ideal for multi app applications where logging to a file is just not convenient.
- Fallback to SQLite in case the DB is not reachable.
- Simple admin interface to view/delete logs included.

## Install

### Composer (preferred)
```
composer require "dereuromark/cakephp-databaselog":"dev-master"
```

## Setup
Enable the plugin in your bootstrap or call
```
bin\cake plugin load DatabaseLog
```

Run the schema into your database:
```
bin\cake schema create --plugin DatabaseLog
```

Optionally create a config setting in your `config/app.php` if you want to use the DB logging approach:
```php
'DatabaseLog' => [
	'datasource' => 'default', // DataSource to read from.
]
```
It is recommended to not use the same datasource as your production server because when the DB is not reachable logging to it will
also not be possible. In that case it will fallback to SQLite file logging on this server instance, though.

Update the file `config/bootstrap.php` with the following configuration:
```php
use Cake\Log\Log

Log::config('default', ['className' => 'DatabaseLog.Database']);
```

## Usage

Anywhere in your app where you call log() or CakeLog::write the DatabaseLog engine will be used.
```php
$this->log('This is a detailed message logged to the database', 'error');
// or
CakeLog::write('error', 'This is a detailed message logged to the database');
```

Navigate to `http://www.example.com/admin/database-log/logs` to view/search/delete your logs.

You can customize the template with a custom theme if necessary.
