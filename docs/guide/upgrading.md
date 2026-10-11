# Upgrading

## Schema and connection

Run plugin migrations against the connection used for logging:

```sh
bin/cake migrations migrate -p DatabaseLog -c database_log
```

Replace `database_log` when using another connection. Existing SQLite files persist across cache clearing and application deployments. Upgrade their schema rather than deleting logs to resolve a missing-column error.

The legacy `DatabaseLog.datasource` alias still works when `connection` is empty. Use `DatabaseLog.connection` in new configuration.

## Auto-creation and failed writes

Since v4.7, table auto-creation defaults to debug mode only. Production should have its schema set up before logging. `DatabaseLog.disableAutoTable = false` explicitly forces runtime creation, while `true` disables it everywhere. See [configuration](../reference/configuration) for the operational tradeoff.

Failed writes now use PHP's `error_log()` rather than escaping into application code. Check the process error log for `[DatabaseLog fallback]` entries during deployment and database outages.

## Admin access and standalone mode

Set `DatabaseLog.adminAccess` to a closure returning the boolean `true` for permitted callers. Standalone mode bypasses host controller initialization and lifecycle callbacks; application middleware still runs. Ensure the callback and middleware enforce the full access rule. See [admin interface](../reference/admin).

## Duplicate counts

Cleanup preserves the counts of previously merged rows. Its output reports duplicate groups merged rather than claiming that each group represents a single removed row. Default grouping remains log type plus summary; use strict grouping from application code when complete messages must match.
