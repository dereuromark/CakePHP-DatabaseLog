# Installation

Install the plugin in an existing CakePHP application:

```sh
composer require dereuromark/cakephp-databaselog:dev-cake6
bin/cake plugin load DatabaseLog
```

Composer checks the supported runtime versions. The [package manifest](https://github.com/dereuromark/CakePHP-DatabaseLog/blob/cake6/composer.json) defines the requirements.

The default connection needs the PDO SQLite driver and a writable application `logs` directory. To use a different database, configure an application datasource and select its name with `DatabaseLog.connection` before plugin bootstrap.

## Migrations

The migrations plugin is optional for development auto-creation, but provides schema setup and upgrades for production. If your application does not have it installed and loaded:

```sh
composer require cakephp/migrations:dev-fix/6.x-cakephp-compatibility
bin/cake plugin load Migrations
```

For the default SQLite connection, use:

```sh
bin/cake migrations migrate -p DatabaseLog -c database_log
```

For another configured connection, replace `database_log` with that connection name. Omitting `-c` selects the application's default migration connection, which may differ from the logging connection.

Continue with [getting started](./getting-started) to configure the log engines and verify a write.
