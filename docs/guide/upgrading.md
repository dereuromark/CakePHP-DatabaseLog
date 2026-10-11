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

## Admin access

Set `DatabaseLog.adminAccess` to a closure returning the boolean `true` for permitted callers. See [admin interface](../reference/admin).

## Unreleased fixes

The following fixes are on the development branch and are not included in v4.7.0.

### Standalone lifecycle

Standalone mode now bypasses host controller initialization and lifecycle callbacks; application middleware still runs. In v4.7.0, host initialization is skipped but host lifecycle callbacks still run. Ensure the callback and middleware enforce the full access rule before upgrading.

### Duplicate counts

Cleanup preserves the counts of previously merged rows and counts legacy zero-count rows as one occurrence each. Its output reports duplicate groups merged rather than claiming that each group represents a single removed row. Default grouping remains log type plus summary; use strict grouping from application code when complete messages must match.

### Custom table subclasses

`removeDuplicates()` declares a boolean parameter and integer return type; `garbageCollector()` declares an integer return type. Update matching overrides in custom subclasses before upgrading.

### Monitor delivery

Monitoring records its query boundary before notification delivery, preserving entries created during the callback at or after that boundary. The next run includes entries on that second boundary, so a boundary entry may appear in two notifications. See [monitoring](../reference/monitoring) for delayed-commit limitations.
