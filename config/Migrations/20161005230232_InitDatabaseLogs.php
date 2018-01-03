<?php
use Migrations\AbstractMigration;

/**
 * Add this for PostgreSQL via:
 * bin/cake Migrations migrate -p DatabaseLog
 *
 * It uses the default database collation and encoding (utf8 or utf8mb4 etc).
 */
class InitDatabaseLogs extends AbstractMigration {

	/**
	 * @return void
	 */
	public function up() {
		if ($this->hasTable('database_logs')) {
			return;
		}

		$this->table('database_logs')
			->addColumn('type', 'string', [
				'default' => null,
				'limit' => 50,
				'null' => false,
			])
			->addColumn('message', 'text', [
				'default' => null,
				'limit' => null,
				'null' => false,
			])
			->addColumn('context', 'text', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('created', 'timestamp', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('ip', 'string', [
				'default' => null,
				'limit' => 50,
				'null' => true,
			])
			->addColumn('hostname', 'string', [
				'default' => null,
				'limit' => 50,
				'null' => true,
			])
			->addColumn('uri', 'string', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('refer', 'string', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('user_agent', 'string', [
				'default' => null,
				'limit' => null,
				'null' => true,
			])
			->addColumn('count', 'integer', [
				'default' => 0,
				'limit' => 10,
				'null' => false,
			])
			->create();
	}

	/**
	 * @return void
	 */
	public function down() {
		$this->dropTable('database_logs');
	}

}
