<?php

/**
 * DatabaseLog Example Configuration
 *
 * Copy this content to your config/app.php
 * and customize it to your needs.
 */
use Cake\Event\EventInterface;
use Cake\Http\ServerRequest;
use Cake\Mailer\Mailer;
use DatabaseLog\Model\Entity\DatabaseLog;

return [
	'DatabaseLog' => [
		'connection' => null, // Connection to use, 'default' will use your live DB instead of SQLite

		// Backend routing (opt-in; defaults to /database-log for BC)
		'routePath' => '/database-log', // Path segment mounted under /admin (e.g. '/logs' for /admin/logs)

		// Admin dashboard options
		'standalone' => false, // Set to true for isolated admin that doesn't depend on the host app
		'adminLayout' => null, // null = plugin layout, false = app layout, string = custom layout
		'dashboardAutoRefresh' => 0, // Auto-refresh interval in seconds (0 = disabled)

		// Admin access gate. REQUIRED — the host app MUST set this to a Closure
		// that returns true to grant access to /admin/database-log/...; anything
		// else (unset, non-Closure, returns false, returns a truthy non-bool, or
		// throws) yields a 403. Log entries commonly contain sensitive data
		// (stack traces with credentials, request bodies with PII, internal
		// paths); the default policy is deny. Independent of `standalone` —
		// runs in both modes.
		// Example — admin role check on the cakephp/authentication identity:
		'adminAccess' => function (ServerRequest $request): bool {
			$identity = $request->getAttribute('identity');

			return $identity !== null && in_array('admin', (array)$identity->roles, true);
		},

		// Monitoring options
		'monitor' => [
			'error',
			'warning',
		],
		'monitorCallback' => function (EventInterface $event): void {
			/** @var \DatabaseLog\Model\Table\DatabaseLogsTable $logsTable */
			$logsTable = $event->getSubject();

			/* @var \DatabaseLog\Model\Entity\DatabaseLog[] $logs */
			$logs = $event->getData('logs');

			$content = '';
			foreach ($logs as $log) {
				$content .= $logsTable->format($log);
			}

			$mailer = new Mailer();
			$subject = count($logs) . ' new error log entries';
			// TODO Implement
		},
		'saveCallback' => function (DatabaseLog $databaseLog) {
			if (empty($_SESSION) || empty($_SESSION['language'])) {
				return;
			}
			$currentSessionLanguage = $_SESSION['language'];

			$databaseLog->message .= PHP_EOL . 'Language: ' . $currentSessionLanguage;
		},
		'disableAutoTable' => null, // Set to true to avoid lazy auto logs table creation
		'isSearchEnabled' => null, // Auto-detect
		'notificationInterval' => 4 * HOUR, // In seconds

		// Cleanup options (used by CleanupCommand / garbageCollector)
		'maxLength' => null, // Global max age, e.g., '-90 days' (null = no limit)
		'limit' => null, // Max number of logs to keep (null = no limit)
		'retention' => [ // Per-type retention policies (overrides maxLength for specific types)
			// 'error' => '-90 days',   // Keep errors for 90 days
			// 'warning' => '-30 days', // Keep warnings for 30 days
			// 'info' => '-7 days',     // Keep info for 7 days
			// 'debug' => '-1 day',     // Keep debug for 1 day
		],
	],
];
