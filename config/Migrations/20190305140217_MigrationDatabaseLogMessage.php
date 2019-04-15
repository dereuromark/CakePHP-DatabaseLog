<?php
use Migrations\AbstractMigration;

class MigrationDatabaseLogMessage extends AbstractMigration
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
			->addColumn('summary', 'string', [
				'default' => null,
				'limit' => 255,
				'null' => false,
			])->update();
	}
}
