<?php

/**
 * DatabaseLog (Default) Configuration
 *
 * Copy this content to your config/app.php
 * and customize it to your needs.
 */

return [
	'DatabaseLog' => [
		'datasource' => null, // DataSource to use, 'default' will use your live DB instead of SQLite
		'monitor' => [
			'error',
			'warning',
		],
		'monitorCallback' => function (\Cake\Event\Event $event) {
			/** @var \DatabaseLog\Model\Table\DatabaseLogsTable $logsTable */
			$logsTable = $event->subject();

			/* @var \DatabaseLog\Model\Entity\DatabaseLog[] $logs */
			$logs = $event->data('logs');

			$content = '';
			foreach ($logs as $log) {
				$content .= $logsTable->format($log);
			}

			$mailer = new \Cake\Mailer\Mailer();
			$subject = count($logs) . ' new error log entries';
			// TODO Implement
		}
	]
];
