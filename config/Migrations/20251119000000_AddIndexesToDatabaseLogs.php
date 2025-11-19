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
		$this->table('database_logs')
			->addIndex(['type'], ['name' => 'type_idx'])
			->addIndex(['created'], ['name' => 'created_idx'])
			->addIndex(['type', 'created'], ['name' => 'type_created_idx'])
			->update();
	}

}
