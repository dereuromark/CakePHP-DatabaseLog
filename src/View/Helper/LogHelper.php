<?php

namespace DatabaseLog\View\Helper;

use Cake\Core\Configure;
use Cake\View\Helper;
use Cake\View\View;

/**
 * @property \Cake\View\Helper\HtmlHelper $Html
 */
class LogHelper extends Helper {

	/**
	 * @var array
	 */
	public $helpers = ['Html'];

	/**
	 * @var array<string, mixed>
	 */
	protected $_defaultConfig = [
		'typeTemplate' => '<span class="badge badge-%s">%s</span>',
		'typeDefaultClass' => 'secondary',
		'typeMap' => [
			'error' => 'danger',
			'cli-error' => 'danger',
			'warning' => 'warning',
			'notice' => 'warning',
			'info' => 'info',
		],
	];

	/**
	 * @param \Cake\View\View $View The View this helper is being attached to.
	 * @param array $config Configuration settings for the helper.
	 */
	public function __construct(View $View, array $config = []) {
		$config += (array)Configure::read('DatabaseLog');

		parent::__construct($View, $config);
	}

	/**
	 * @param string $type
	 * @return string Formatted HTML
	 */
	public function typeLabel($type) {
		$map = $this->getConfig('typeMap');
		if (!$map) {
			return (string)h($type);
		}

		$class = $this->getConfig('typeDefaultClass');
		if (!empty($map[$type])) {
			$class = $map[$type];
		}

		$template = $this->getConfig('typeTemplate');

		return sprintf($template, $class, h($type));
	}

}
