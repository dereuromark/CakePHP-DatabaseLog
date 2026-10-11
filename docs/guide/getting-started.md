# Getting started

After [installation](./installation), the easiest local setup uses the default SQLite connection. In debug mode, the plugin creates its table on first use. In production, run the migration first as described in installation.

## Configure logging

Replace the `debug` and `error` entries in your existing `Log` configuration in `config/app.php` or `config/app_local.php`:

```php
'Log' => [
    'debug' => [
        'className' => 'DatabaseLog.Database',
        'levels' => ['notice', 'info', 'debug'],
        'scopes' => null,
    ],
    'error' => [
        'className' => 'DatabaseLog.Database',
        'levels' => ['warning', 'error', 'critical', 'alert', 'emergency'],
        'scopes' => null,
    ],
],
```

Keep any other log engines your application needs. Remove the old `file` key from converted file-log entries: DatabaseLog treats it as a custom type override. The `path` and rotation settings belong to file logging and are not needed here.

## Write and inspect an entry

From your application root:

```sh
bin/cake database_logs show --test-entry warning
```

The command writes a test warning through the configured CakePHP log engines and lists recent entries. If it does not appear, check the selected log levels, the connection, schema setup, and the process error log for `[DatabaseLog fallback]` messages.

Your application can now use its normal logging calls:

```php
\Cake\Log\Log::warning('A background task needs attention', ['taskId' => 42]);
```

The log engine stores context as JSON. It adds available web or CLI metadata, but does not redact secrets; choose what you log accordingly.

## Optional admin viewer

Configure your application's access rule before opening the viewer. For an application whose authenticated identity has a `roles` field:

```php
'DatabaseLog' => [
    'adminAccess' => function (\Cake\Http\ServerRequest $request): bool {
        $identity = $request->getAttribute('identity');

        return $identity !== null && in_array('admin', (array)$identity->roles, true);
    },
],
```

Adapt that callback to your actual identity and role model. It grants access only when it returns the boolean `true`. Without a valid callback, admin requests return 403.

With plugin routes enabled, visit `/admin/database-log` for the dashboard or `/admin/database-log/logs` for entries. Keep the host application's authentication middleware. See the [admin reference](../reference/admin) before enabling standalone mode.

## Keep the table manageable

Set retention appropriate for your application, for example:

```php
'DatabaseLog' => [
    'maxLength' => '-30 days',
    'limit' => 100000,
],
```

Merge these values into the existing `DatabaseLog` configuration rather than replacing the access callback above. Schedule `bin/cake database_logs cleanup` to remove old rows and merge duplicates. Retention settings take effect when cleanup runs, not on each log write. See [commands](../reference/commands) for its grouping behavior and other maintenance tasks.
