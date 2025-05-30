<?php

/**
 * DatabaseLog (Default) Configuration
 *
 * Copy this content to your config/app.php
 * and customize it to your needs.
 */
use Cake\Event\EventInterface;
use Cake\Mailer\Mailer;
use DatabaseLog\Model\Entity\DatabaseLog;

return [
	'DatabaseLog' => [
		'connection' => null, // Connection to use, 'default' will use your live DB instead of SQLite
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
	],
];
