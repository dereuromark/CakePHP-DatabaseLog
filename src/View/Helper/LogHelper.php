<?php
namespace DatabaseLog\View\Helper;

use Cake\Core\Configure;
use Cake\View\Helper;
use Cake\View\View;

/**
 * @property \Cake\View\Helper\HtmlHelper $Html;
 */
class LogHelper extends Helper {

	/**
	 * @var array
	 */
	public $helpers = ['Html'];

	/**
	 * @var array
	 */
	protected $_defaultConfig = [
		'template' => '<span class="label label-%s">%s</span>',
		'defaultClass' => 'default',
		'map' => [
			'error' => 'danger',
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
		$class = $this->config('defaultClass');
		if (!empty($this->_config['map'][$type])) {
			$class = $this->_config['map'][$type];
		}

		$template = $this->config('template');
		return sprintf($template, $class, h($type));
	}

}
