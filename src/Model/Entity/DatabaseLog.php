<?php

namespace DatabaseLog\Model\Entity;

use Cake\ORM\Entity;

/**
 * @property int $id
 * @property string $type
 * @property string $message
 * @property string|null $context
 * @property string|null $ip
 * @property string|null $hostname
 * @property string|null $uri
 * @property string|null $refer
 * @property string|null $user_agent
 * @property int $count
 * @property \Cake\I18n\FrozenTime $created
 * @property string $summary
 */
class DatabaseLog extends Entity {

	/**
	 * Fields that can be mass assigned using newEntity() or patchEntity().
	 *
	 * Note that when '*' is set to true, this allows all unspecified fields to
	 * be mass assigned. For security purposes, it is advised to set '*' to false
	 * (or remove it), and explicitly make individual fields accessible as needed.
	 *
	 * @var array<string, bool>
	 */
	protected $_accessible = [
		'*' => true,
		'id' => false,
	];

	/**
	 * @return bool
	 */
	public function isCli() {
		return $this->uri && strpos($this->uri, 'CLI ') === 0;
	}

}
