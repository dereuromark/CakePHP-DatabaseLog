# Configuration

Put plugin settings under `DatabaseLog` in your application configuration. They must be available before plugin bootstrap and before its table or log engine is first created. Engine settings belong in individual `Log` entries.

## Storage

| Setting | Default | Behavior |
| --- | --- | --- |
| `connection` | `null` | Selects an existing datasource by name; otherwise uses `database_log`. |
| `disableAutoTable` | `null` | Creates a missing table only in debug mode. `true` disables creation; `false` forces it in every environment. |
| `saveCallback` | `null` | Callable receiving the entity immediately before save, after metadata is populated. |

For a dedicated database connection already defined in `Datasources`:

```php
'DatabaseLog' => [
    'connection' => 'logging',
],
```

Run migrations with `-c logging`. Prefer a separate connection when you need logs available independently of the application's main database. A failed write uses PHP's `error_log()`; it does not fail over to SQLite.

Default SQLite storage is `LOGS . 'database_log.sqlite'`. This file is local to the server; it is not shared automatically across servers or preserved automatically by ephemeral deployments. Database permissions, backups, and persistence remain application responsibilities.

Runtime auto-creation uses schema inspection. Keeping it disabled in production avoids schema checks on the logging path when the metadata cache or database is unavailable. Explicitly enabling it requires permission to create tables. Auto-creation creates a missing table; it does not migrate an existing schema.

## Log engine

| Setting | Default | Behavior |
| --- | --- | --- |
| `className` | Required | Use `DatabaseLog.Database`. |
| `levels` | `[]` | CakePHP level selection; empty accepts all levels. |
| `scopes` | `[]` | CakePHP scope selection; empty accepts all scopes, `null` accepts only unscoped messages. |
| `type` | Unset | Overrides the stored type for this engine. |
| `file` | Unset | Legacy type override, used only when `type` is empty. It does not create a file. |
| `model` | `DatabaseLog.DatabaseLogs` | Table alias used for writes. A replacement must provide a compatible `log()` method. |

Use `null` rather than the deprecated `false` value for unscoped-only logging. The engine stores the original message and JSON context; it does not interpolate context placeholders or apply file-log formatting and rotation settings.

For a custom category, add a separate engine:

```php
'Log' => [
    'bounce' => [
        'className' => 'DatabaseLog.Database',
        'type' => 'bounce',
        'levels' => ['info'],
        'scopes' => ['bounce'],
    ],
],
```

Write an entry with the matching scope:

```php
\Cake\Log\Log::info('Delivery failed', ['scope' => ['bounce']]);
```

Other engines with matching levels and `scopes = []` receive that entry too. Set their scopes to `null` if they should accept only unscoped messages.

## Cleanup

| Setting | Default | Behavior |
| --- | --- | --- |
| `maxLength` | `null` | Global age cutoff, such as `-30 days`; unset disables global age cleanup. |
| `limit` | `999999` | Maximum rows kept by cleanup. Empty values, including `null` and `0`, use the default. |
| `retention` | Unset | Per-type age cutoffs overriding the global cutoff. |

```php
'DatabaseLog' => [
    'maxLength' => '-30 days',
    'limit' => 100000,
    'retention' => [
        'error' => '-90 days',
        'debug' => '-1 day',
    ],
],
```

Cleanup applies per-type policies first, then the global age cutoff to other types, then the row limit. A type listed with an empty retention value is exempt from age cleanup but still subject to the total row limit. The row limit keeps the highest IDs; it does not sum occurrence counts.

## Save callback

```php
'DatabaseLog' => [
    'saveCallback' => function (\DatabaseLog\Model\Entity\DatabaseLog $entry): void {
        $entry->message .= PHP_EOL . 'Deployment: ' . (getenv('APP_RELEASE') ?: 'unknown');
    },
],
```

Keep this callback small and avoid logging from inside it. It runs for each entity save, after the summary has already been generated. Modifying the message does not recalculate the summary automatically.
