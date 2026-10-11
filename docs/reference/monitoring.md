# Monitoring

The monitor command finds recent entries of configured types and dispatches `DatabaseLog.alert`. Supply a callback that delivers the notification; the plugin does not send mail automatically.

For an application with a working default mail transport:

```php
'DatabaseLog' => [
    'monitor' => ['error', 'warning'],
    'notificationInterval' => 14400,
    'monitorCallback' => function (\Cake\Event\EventInterface $event): void {
        /** @var \DatabaseLog\Model\Table\DatabaseLogsTable $table */
        $table = $event->getSubject();
        $entries = $event->getData('logs');
        $body = '';
        foreach ($entries as $entry) {
            $body .= $table->format($entry);
        }
        $mailer = new \Cake\Mailer\Mailer();
        $mailer->setFrom('logs@example.com');
        $mailer->setTo('admin@example.com');
        $mailer->setSubject(count($entries) . ' new log entries');
        $mailer->deliver($body);
    },
],
```

Replace the addresses and configure your application's mail transport. `notificationInterval` is required and measured in seconds; this example permits notifications every four hours. The `monitor` list must contain at least one stored type.

Run the command periodically:

```text
*/5 * * * * cd /var/www/my-app && bin/cake database_logs monitor -q
```

The command stores its last reported timestamp in `LOGS . 'export'`. The first run examines all matching entries. Subsequent runs select entries created after that timestamp and respect the notification interval. With no matching entries, it reports nothing and leaves the timestamp unchanged. The marker is local to the server and must be writable; do not run overlapping monitor jobs.

The event contains the selected entities in its `logs` data key and the table as its subject. Instead of a configured callback, register an application listener:

```php
\Cake\Event\EventManager::instance()->on('DatabaseLog.alert', $callback);
```

Define `$callback` with the same event signature as the example. The marker is updated after event dispatch even if no listener is registered, so configure delivery before enabling the cron job. An exception from delivery aborts the command before the marker is updated. Monitoring does not deduplicate entries or clean up the table.
