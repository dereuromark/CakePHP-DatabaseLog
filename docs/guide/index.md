# Overview

DatabaseLog is a CakePHP log engine with an admin viewer and maintenance commands. It stores the message, JSON context, log type, timestamp, and available request or command-line metadata.

Without a configured connection, the plugin defines a local SQLite connection named `database_log` using `logs/database_log.sqlite`. In debug mode, the table is created when first used. Production requires schema setup before writing logs.

You can instead select an existing MySQL, PostgreSQL, or SQLite connection. Failed writes go to PHP's `error_log()`; the plugin does not automatically switch an unavailable remote database to SQLite.

The admin interface requires an access callback. Logging and CLI commands can be used without enabling the viewer. Monitoring requires your own notification callback; the plugin does not configure mail recipients or send alerts by itself.

Start with [installation](./installation), then [write your first entry](./getting-started).
