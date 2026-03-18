<?php

use Migrations\BaseMigration;

class AddIndexesToDatabaseLogs extends BaseMigration {

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

		// Skip if indexes already exist
		if ($table->hasIndexByName('type_idx')) {
			return;
		}

		$table
			->addIndex(['type'], ['name' => 'type_idx'])
			->addIndex(['created'], ['name' => 'created_idx'])
			->addIndex(['type', 'created'], ['name' => 'type_created_idx'])
			->update();
	}

}
