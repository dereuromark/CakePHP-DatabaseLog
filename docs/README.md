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

It will combine the log entries of the same content (and increase the count), and on top
also clean out old records, either by date or by total record count.


## Contributing
Feel free to fork and pull request.

There are a few guidelines:

- Coding standards passing: `vendor/bin/sniff` and `vendor/bin/sniff -f`
- Tests passing for Windows and Unix: `php phpunit.phar`


