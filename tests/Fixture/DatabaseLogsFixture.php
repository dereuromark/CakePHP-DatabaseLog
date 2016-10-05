<?php
/**
 * CakePHP DatabaseLog Plugin
 *
 * Licensed under The MIT License.
 *
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 * @link https://github.com/dereuromark/CakePHP-DatabaseLog
 */
namespace DatabaseLog\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * Log fixtures
 */
class DatabaseLogsFixture extends TestFixture {

	/**
	 * Fields
	 *
	 * @var array
	 */
	public $fields = [
		'id' => ['type' => 'integer', 'null' => false, 'default' => null],
		'type' => ['type' => 'string', 'null' => false, 'default' => null, 'length' => 50],
		'message' => ['type' => 'text', 'null' => false, 'default' => null],
		'context' => ['type' => 'text', 'null' => true, 'default' => null],
		'created' => ['type' => 'datetime', 'null' => true, 'default' => null],
		'ip' => ['type' => 'string', 'null' => true, 'default' => null, 'length' => 50],
		'hostname' => ['type' => 'string', 'null' => true, 'default' => null, 'length' => 50],
		'uri' => ['type' => 'string', 'null' => true, 'default' => null],
		'refer' => ['type' => 'string', 'null' => true, 'default' => null],
		'user_agent' => ['type' => 'string', 'null' => true, 'default' => null],
		'count' => ['type' => 'integer', 'null' => false, 'default' => 0],
		'_constraints' => ['primary' => ['type' => 'primary', 'columns' => ['id']]],
		'_options' => []
	];

	/**
	 * Records
	 *
	 * @var array
	 */
	public $records = [
	];

}
