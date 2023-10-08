<?php

namespace DatabaseLog;

use Cake\Console\CommandCollection;
use Cake\Core\BasePlugin;
use Cake\Routing\RouteBuilder;
use DatabaseLog\Command\CleanupCommand;
use DatabaseLog\Command\ExportCommand;
use DatabaseLog\Command\MonitorCommand;
use DatabaseLog\Command\ResetCommand;
use DatabaseLog\Command\ShowCommand;

/**
 * Plugin for DatabaseLog
 */
class DatabaseLogPlugin extends BasePlugin {

	/**
	 * @var bool
	 */
	protected bool $middlewareEnabled = false;

	/**
	 * @param \Cake\Routing\RouteBuilder $routes The route builder to update.
	 * @return void
	 */
	public function routes(RouteBuilder $routes): void {
		$routes->prefix('Admin', function (RouteBuilder $routes): void {
			$routes->plugin('DatabaseLog', ['path' => '/database-log'], function (RouteBuilder $routes): void {
				$routes->connect('/', ['controller' => 'DatabaseLog', 'action' => 'index']);

				$routes->fallbacks();
			});
		});
	}

	/**
	 * @inheritDoc
	 */
	public function console(CommandCollection $commands): CommandCollection {
		$commands->add('database_logs show', ShowCommand::class);
		$commands->add('database_logs monitor', MonitorCommand::class);
		$commands->add('database_logs cleanup', CleanupCommand::class);
		$commands->add('database_logs reset', ResetCommand::class);
		$commands->add('database_logs export', ExportCommand::class);

		return $commands;
	}

}
