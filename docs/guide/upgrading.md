# Upgrading

## CakePHP 6 development branch

The `cake6` branch targets CakePHP 6. Custom commands override `execute()` without arguments and read `$this->args` and `$this->io`. Host controller lifecycle callbacks return `void`; use `$event->setResult($response)` for an authentication redirect. DatabaseLog preserves that response before applying its own access callback.

Entity mass-assignment configuration uses `$patchable`, helper defaults use `$defaultConfig`, and queries use `enableHydration(false)` for array results. Update application overrides to the corresponding CakePHP 6 contracts.

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

## Cleanup method contracts prepared for CakePHP 6

The `cake6` development work adds native declarations to `removeDuplicates(bool $strict = false): int` and `garbageCollector(): int`. Pass an actual boolean to `removeDuplicates()`, including from files with strict types enabled. Null and other non-boolean inputs are not part of that future contract.

CakePHP 5 retains the released method declarations. This preparation still uses CakePHP 5 dependencies for verification and does not promise CakePHP 6 runtime support.
