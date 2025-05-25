<?php

use Migrations\BaseMigration;

class AlterDatabaseLogs extends BaseMigration {

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
		$table->changeColumn('uri', 'text', [
			'default' => null,
			'null' => true,
		]);
		$table->update();
	}

}
