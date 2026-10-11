# Commands

The registered command prefix is `database_logs`, with a plural `logs`. Run commands from the application root so they load its plugin and logging configuration.

## Show

```sh
bin/cake database_logs show
bin/cake database_logs show error,warning --limit 50,100 --verbose
bin/cake database_logs show --test-entry warning
```

The optional argument is a comma-separated list of stored types. `--limit`/`-l` accepts a row limit, or `offset,limit`; the default is 20 rows. Entries are ordered by creation time, newest first. Verbose output adds database information and full messages.

`--test-entry`/`-t` writes a fixed test message through the application's logging configuration before listing entries. Use `warning:bounce` to supply a scope; its level must be a supported CakePHP log level. The stored type depends on the selected engine's type override.

## Cleanup

```sh
bin/cake database_logs cleanup
```

Cleanup removes entries according to age policies and the row limit, then merges duplicate groups. It runs only when invoked; writing a log does not trigger retention or deduplication.

Default grouping uses the stored type and the truncated summary, so messages with the same first portion can merge even if their full bodies or contexts differ. The newest row's message and metadata remain, and occurrence counts are summed. Run only one cleanup worker at a time.

Application code can request strict grouping by complete message:

```php
$logs = \Cake\ORM\TableRegistry::getTableLocator()->get('DatabaseLog.DatabaseLogs');
$logs->removeDuplicates(true);
```

Strict grouping still groups by type and does not compare context or metadata. The method returns the number of merged groups, not the number of removed rows. Age and row-limit cleanup run before merging, so entries removed by retention do not contribute to surviving counts.

For an hourly cron job, adjust the application path:

```text
0 * * * * cd /var/www/my-app && bin/cake database_logs cleanup -q
```

## Export

```sh
bin/cake database_logs export
bin/cake database_logs export error,warning --limit 2000
```

Export writes `LOGS . 'export-{type}.txt'`, replacing an existing export for each selected type. With no type argument, it exports all stored types. The default limit is 100 entries per type; `--limit`/`-l` accepts a row count, without an offset.

Type names are sanitized for filenames. Entries are newest first, and the export contains formatted messages rather than every database field. It neither deletes entries nor changes monitoring state. These exports can contain sensitive log content; protect the application's logs directory.

## Reset

```sh
bin/cake database_logs reset
```

Reset truncates all entries and asks for confirmation. `--quiet`/`-q` skips that confirmation. `--test-entry`/`-t` writes a test entry after truncation through the configured log engines. The legacy `--limit` option is accepted but does not limit deletion.

## Monitor

```sh
bin/cake database_logs monitor
```

See [monitoring](./monitoring) for required configuration and alert delivery.
