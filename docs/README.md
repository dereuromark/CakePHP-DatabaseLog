#  CakePHP DatabaseLog Plugin Documentation


## Testing log writing

You can use the shell to quickly produce log entries in your database:
```
bin/cake database_log test_entry

bin/cake database_log test_entry warning Warning!
```


## Cleanup
It is highly recommended to add some kind of log rotation or cleanup procedure.

You can install a cronjob to hourly trigger the cleanup shell command:
```
bin/cake database_log cleanup
```
See the [CakePHP Cronjob docs](https://book.cakephp.org/4.0/en/console-and-shells/cron-jobs.html) for details.

It will combine the log entries of the same content (and increase the count), and on top
also clean out old records, either by date or by total record count.

You can adjust both values in your app.php config:
```php
	'DatabaseLog' => [
		'limit' => 999999,
		'maxLength' => '-1 year', // Older than a year
```
The `limit` config defaults to `999999` as basic protection. The `maxLength` is disabled by default.

## Backend View
Navigate to `http://your-domain.local/admin/database-log/logs` to view/search/delete your logs.
Make sure you loaded your plugin with `'routes' => true'` in that case.

You can customize the template with a custom theme if necessary.

You can also adjust the label colors of the log types with Configure and
```php
	'DatabaseLog' => [
		'typeTemplate' => '...', // Custom template (defaults to bootstrap markup)
		'typeDefaultClass => '...', // Custom class (defaults to bootstrap markup)
		'typeMap' => [
			// Custom class mapping (defaults to bootstrap markup)
		],
	],
```
If you want to disable it completely, set `'map'` to `false`.

## CLI View
A very basic command line view is also available, especially if the web backend is not available (or the whole site in maintenance mode).
```
bin/cake database_log show [type]
```

Options:
- `-l`/`--limit`, e.g. `-l 50,100` (defaults to 20)
- `-v`/`--verbose` for full content

## Generating text files
In case you want store them in a different format, you can generate for example TXT files per type:
```
bin/cake database_log export [type]
```
They will be put into the same `/logs` folder by default. Type `error` would then become a `export-error.txt` file.

Options:
- `-l`/`--limit`, e.g. `-l 2000` (defaults to 100)

They are on purpose `.txt` as they are not typical log files, the order is reversed (latest ones on top) just as with `show` output.

## Resetting
```
bin/cake database_log reset
```
will truncate your logs table and you have a fully resetted setup.

## Save Callback
You can add additional infos into the stacktrace via custom `saveCallback` callable:
```php
// in your app.php config
	'DatabaseLog' => [
		'saveCallback' => function (\DatabaseLog\Model\Entity\DatabaseLog $databaseLog) {
			if (empty($_SESSION) || empty($_SESSION['language'])) {
				return;
			}
			$currentSessionLanguage = $_SESSION['language'];

			$databaseLog->message .= PHP_EOL . 'Language: ' . $currentSessionLanguage;
		},
	],
```
This will run after all the internal processing of the entity has been done, prior to actually saving the log.

## Monitor
You can run a very basic cronjob based monitoring on your log files, alerting you via eMail, SMS or alike if any critical issues arise.
Just enable it via Configure:
```php
	'DatabaseLog' => [
		'monitor' => [
			'error',
			'warning',
			'notice',
			...
		],
		'notificationInterval' => 4 * HOUR, // In seconds
		'monitorCallback' => function (\Cake\Event\EventInterface $event) {
			/** @var \DatabaseLog\Model\Table\DatabaseLogsTable $logsTable */
			$logsTable = $event->getSubject();

			/* @var \DatabaseLog\Model\Entity\DatabaseLog[] $logs */
			$logs = $event->getData('logs');

			$content = '';
			foreach ($logs as $log) {
				$content .= $logsTable->format($log);
			}

			$mailer = new \Cake\Mailer\Mailer();
			$subject = count($logs) . ' new error log entries';
			// TODO Implement
		},
	],
```

The `notificationInterval` is important to set high enough, so you won't get every x minutes a new email.

The callback via Configure is optional, you can also directly attach any method of your classes to the fired `DatabaseLog.alert` event:
```php
class AlertTheAdmin implements EventListenerInterface {

	public function implementedEvents() {
		return [
			'DatabaseLog.alert' => 'methodToRun',
		];
	}

	public function methodToRun($event, $entity) {
		...
	}

}

// Attach it somewhere
...->getEventManager()->on(new AlertTheAdmin());
```

You could even have it manually set up in your bootstrap:
```php
EventManager::instance()->on(
	'DatabaseLog.alert',
	$myCallback
);
```
Read more about this in the [CakePHP docs](https://book.cakephp.org/4.0/en/core-libraries/events.html).

To set up a cronjob here, you can for example use crontab:
```
crontab -u www-data -e
```
And set up something like
```
*/5 * * * * cd /var/www/my-app/ && bin/cake database_log monitor -q
```
This example would make the cronjob look every 5 minutes of there is something to alert about.

## Setting up a custom type
Let's say, you want to collect the (email) bounces, and for that you want to use an own type.
For filesystem this would then by `bounce.log` (using `'file' => 'bounce'` config).

With this plugin it is as easy as scoping it and having a listener for this scope:
```php
$this->log($email, 'info', ['scope' => 'bounce']);
```

And this would be your config:
```php
	'Log' => [
		...
		'bounce' => [
			'className' => 'DatabaseLog.Database',
			'type' => 'bounce', // also works with FileLog `file` key
			'levels' => ['info'],
			'scopes' => ['bounce'],
		],
	],
```

Now you can see all those in a specific new type category in your backend (probably /admin/database-log/logs?type=bounce).

## Backend configuration

- isSearchEnabled: Set to false if you do not want search/filtering capability.
This is auto-detected based on [Search](https://github.com/FriendsOfCake/search) plugin being available/loaded if not disabled.
If disabled, it will at least still be able to filter by error type.

## Tips

### 404 logs should not be part of your error log.
See [Tools plugin ErrorHandler documentation](https://github.com/dereuromark/cakephp-tools/blob/master/docs/Error/ErrorHandler.md).

### Looking into more advanced toolings
This is a basic tool and sure some improvement over log files. It works out of the box without hassle.
But even so there are more professional tools like Monolog/NewRelic which can be included with plugins and provide more enterprise-ready solutions beyond what this plugin can ever offer.

### Disabling for tests
If you do not want an extra "test_database_log" database for testing, you can specifically for testing go back to files again.
Just read the Log configs, set the `'className'` back to `'File'` for each and dropConfig() + setConfig() them.
Then your tests should just use basic file writing for logs again.

## Troubleshooting

Make sure you clear the cache, and also remove the local sqlite DB (as this doesn't get cleared automatically).
Especially when you get errors after upgrading, like `General error: 1 no such column: DatabaseLogs.summary` etc.

## Contributing
Feel free to fork and pull request.

There are a few guidelines:

- Coding standards passing: `composer cs-check` to check and `composer cs-fix` to fix.
- Tests passing for Windows and Unix: `composer test` to run them.
