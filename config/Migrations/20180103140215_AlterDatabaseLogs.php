<?php
use Migrations\AbstractMigration;

class AlterDatabaseLogs extends AbstractMigration
{
	/**
	 * Change Method.
	 *
	 * More information on this method is available here:
	 * http://docs.phinx.org/en/latest/migrations.html#the-change-method
	 * @return void
	 */
	public function change()
	{
		$table = $this->table('database_logs');
		$table->changeColumn('uri', 'text', [
			'default' => null,
			'null' => true,
		]);
		$table->update();
	}
}
