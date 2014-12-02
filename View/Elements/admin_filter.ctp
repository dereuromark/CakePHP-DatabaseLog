<?php
/**
 * CakePHP DatabaseLog Plugin
 *
 * Licensed under The MIT License.
 *
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 * @link https://github.com/dereuromark/CakePHP-DatabaseLog
 */

echo $this->Html->script('/database_log/js/clear_default');
?>
<div id="admin_filter">
<?php
$model = isset($model) ? $model : false;

if ($model) {
	echo $this->Form->create($model, array('inputDefaults' => array('label' => false, 'div' => false)));
	echo $this->Form->input('filter', array('label' => false, 'placeholder' => "$model Search", 'class' => 'clear_default'));
	echo $this->Form->submit('/database_log/img/search_button.gif', array('div' => false));
	echo $this->Form->end();
}
?>
</div>