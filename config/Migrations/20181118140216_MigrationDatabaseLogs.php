<?php
use Migrations\AbstractMigration;

class MigrationDatabaseLogs extends AbstractMigration
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
		$this->table('database_logs')
			->changeColumn('ip', 'string', [
				'default' => null,
				'limit' => 100,
				'null' => true,
			])
			->changeColumn('hostname', 'string', [
				'default' => null,
				'limit' => 100,
				'null' => true,
			])
			->changeColumn('created', 'datetime', [
				'default' => null,
				'null' => false,
			])->update();
	}
}
