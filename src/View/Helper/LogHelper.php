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
	 * @var array
	 */
	protected $_defaultConfig = [
		'typeTemplate' => '<span class="label label-%s">%s</span>',
		'typeDefaultClass' => 'default',
		'typeMap' => [
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
		$map = $this->config('typeMap');
		if (!$map) {
			return h($type);
		}

		$class = $this->config('typeDefaultClass');
		if (!empty($map[$type])) {
			$class = $map[$type];
		}

		$template = $this->config('typeTemplate');
		return sprintf($template, $class, h($type));
	}

}
