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
		// 'datasource' => null, // DEPRECATED fallback alias for 'connection'. Used only when 'connection' is empty. Use 'connection' instead. When both are empty, defaults to the 'database_log' connection.

		// Admin route mount (defaults to /database-log)
		'routePath' => '/database-log', // Path segment mounted under /admin (e.g. '/logs' for /admin/logs)

		// Admin dashboard options
		'standalone' => false, // Skip host controller initialization and lifecycle callbacks
		'adminLayout' => null, // null = plugin layout, false = app layout, string = custom layout
		'dashboardAutoRefresh' => 0, // Auto-refresh interval in seconds (0 = disabled)

		// Back-to-App link in the admin header (opt-in). When set, an outline
		// "Back to App" button appears in the header so admins can escape the
		// plugin-isolated layout. Accepts anything Router::url() takes — Cake
		// URL array, path string, or full URL. Use 'plugin' => false to
		// anchor the builder to the host app.
		// 'adminBackUrl' => ['plugin' => false, 'prefix' => 'Admin', 'controller' => 'Overview', 'action' => 'index'],
		// 'adminBackLabel' => 'Back to admin', // Optional. Defaults to "Back to App".

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

			// Replace these addresses and configure the application's mail transport.
			$mailer = new Mailer();
			$mailer->setFrom('logs@example.com');
			$mailer->setTo('admin@example.com');
			$mailer->setSubject(count($logs) . ' new log entries');
			$mailer->deliver($content);
		},
		'saveCallback' => function (DatabaseLog $databaseLog): void {
			if (empty($_SESSION) || empty($_SESSION['language'])) {
				return;
			}
			$currentSessionLanguage = $_SESSION['language'];

			$databaseLog->message .= PHP_EOL . 'Language: ' . $currentSessionLanguage;
		},
		// Lazy auto-creation of the logs table. null (default) enables it in debug mode only.
		// Set to true to disable it everywhere, false to force it on also in production.
		'disableAutoTable' => null,
		'isSearchEnabled' => null, // Auto-detect
		'notificationInterval' => 14400, // Four hours, in seconds

		// Cleanup options (used by CleanupCommand / garbageCollector)
		'maxLength' => null, // Global max age, e.g., '-90 days' (null = no limit)
		'limit' => null, // Max rows to keep (null uses the default limit of 999999)
		'retention' => [ // Per-type retention policies (overrides maxLength for specific types)
			// 'error' => '-90 days',   // Keep errors for 90 days
			// 'warning' => '-30 days', // Keep warnings for 30 days
			// 'info' => '-7 days',     // Keep info for 7 days
			// 'debug' => '-1 day',     // Keep debug for 1 day
		],
	],
];
