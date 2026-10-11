<?php

namespace DatabaseLog\Command;

use Cake\Command\Command;
use DatabaseLog\Model\Table\DatabaseLogsTable;
use LogicException;

abstract class DatabaseLogCommand extends Command {

	/**
	 * @return \DatabaseLog\Model\Table\DatabaseLogsTable
	 */
	protected function logs(): DatabaseLogsTable {
		$table = $this->fetchTable('DatabaseLog.DatabaseLogs');
		if (!$table instanceof DatabaseLogsTable) {
			throw new LogicException('DatabaseLog.DatabaseLogs must extend DatabaseLogsTable.');
		}

		return $table;
	}

}
