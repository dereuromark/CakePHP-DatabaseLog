<?php
/**
 * CakePHP DatabaseLog Plugin
 *
 * Licensed under The MIT License.
 *
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 * @link https://github.com/dereuromark/CakePHP-DatabaseLog
 * @var \App\View\AppView $this
 */

//echo $this->Html->script('/database_log/js/clear_default');
?>
<div id="admin-filter">
<?php
$model = isset($model) ? $model : false;

if ($model) {
	echo $this->Form->create();
	echo $this->Form->input('filter', ['label' => false, 'placeholder' => "$model Search", 'class' => 'clear-default']);
	echo $this->Form->submit(__('Filter'), ['div' => false]);
	echo $this->Form->end();
}
?>
</div>
