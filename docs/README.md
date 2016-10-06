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
See the [CakePHP Cronjob docs](http://book.cakephp.org/3.0/en/console-and-shells/cron-jobs.html) for details.

It will combine the log entries of the same content (and increase the count), and on top
also clean out old records, either by date or by total record count.

You can adjust both values in your app.php config:
```php
	'DatabaseLog' => [
		'limit' => 999999,
		'maxLength' => '-1 year' // Older than a year
```
The `limit` config defaults to `999999` as basic protection. The `maxLength` is disabled by default.

## Resetting
```
bin/cake database_log reset
```
will truncate your logs table and you have a fully resetted setup.

## Contributing
Feel free to fork and pull request.

There are a few guidelines:

- Coding standards passing: `vendor/bin/sniff` to check and `vendor/bin/sniff -f` to fix.
- Tests passing for Windows and Unix: `php phpunit.phar` to run them.

