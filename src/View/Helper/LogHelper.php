<?php
namespace DatabaseLog\View\Helper;

use Cake\View\Helper;

/**
 * @property \Cake\View\Helper\HtmlHelper $Html;
 */
class LogHelper extends Helper {

	/**
	 * @var array
	 */
	public $helpers = ['Html'];

	/**
	 * @param string $type
	 * @return string Formatted HTML
	 */
	public function typeLabel($type) {
		switch ($type) {
			case 'error':
				return '<span class="label label-danger">' . h($type) .'</span>';
			case 'warning':
			case 'notice':
				return '<span class="label label-warning">' . h($type) .'</span>';
			case 'info':
				return '<span class="label label-info">' . h($type) .'</span>';
		}

		return h($type);
	}

}
