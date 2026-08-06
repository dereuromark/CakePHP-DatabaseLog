# Upgrading from older versions

For v4.7+, lazy auto-creation of the `database_logs` table only happens in `debug` mode.
Previously it ran in every environment unless `DatabaseLog.disableAutoTable` was set to `true`.

If you relied on the table being created for you in production, either run the migration once:
```
bin/cake migrations migrate -p DatabaseLog
```

or restore the old behavior explicitly:
```php
	'DatabaseLog' => [
		'disableAutoTable' => false,
	],
```

See [Auto-creation of the logs table](README.md#auto-creation-of-the-logs-table) for why the
default changed.

For v4.1+, the `datasource` key has been renamed to `connection` to be consistent
with naming conventions in general here.

