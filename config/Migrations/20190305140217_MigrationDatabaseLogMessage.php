<?php

use Migrations\BaseMigration;

class MigrationDatabaseLogMessage extends BaseMigration {

	/**
	 * Change Method.
	 *
	 * More information on this method is available here:
	 * http://docs.phinx.org/en/latest/migrations.html#the-change-method
	 *
	 * @return void
	 */
	public function change() {
		$table = $this->table('database_logs');

		// Skip if summary column already exists
		if ($table->hasColumn('summary')) {
			return;
		}

		$table
			->addColumn('summary', 'string', [
				'default' => null,
				'limit' => 255,
				'null' => false,
			])->update();
	}

}
