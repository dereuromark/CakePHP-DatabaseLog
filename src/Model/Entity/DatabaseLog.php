<?php
namespace DatabaseLog\Model\Entity;

use Cake\ORM\Entity;

/**
 * @property int $id
 * @property string $type
 * @property string $message
 * @property string $context
 * @property string $ip
 * @property string $hostname
 * @property string $uri
 * @property string $refer
 * @property string $user_agent
 * @property int $count
 * @property \Cake\I18n\FrozenTime $created
 */
class DatabaseLog extends Entity {

	/**
	 * Fields that can be mass assigned using newEntity() or patchEntity().
	 *
	 * Note that when '*' is set to true, this allows all unspecified fields to
	 * be mass assigned. For security purposes, it is advised to set '*' to false
	 * (or remove it), and explicitly make individual fields accessible as needed.
	 *
	 * @var array
	 */
	protected $_accessible = [
		'*' => true,
		'id' => false,
	];

}
