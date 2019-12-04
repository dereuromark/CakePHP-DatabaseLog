<?php
/**
 * @var \App\View\AppView $this
 * @var \DatabaseLog\Model\Entity\DatabaseLog[] $logs
 * @var int[] $typesWithCount
 * @var array $lastErrors
 */

use DatabaseLog\Model\Table\DatabaseLogsTable;

?>

<nav class="large-3 medium-4 columns col-lg-3 col-md-4 actions">
	<ul class="side-nav">
		<li class="heading"><?= __('Actions') ?></li>
		<li><?php echo $this->Form->postLink(__('Remove {0}', __('Duplicates')), ['controller' => 'Logs', 'action' => 'removeDuplicates']); ?></li>
		<li><?php echo $this->Form->postLink(__('Remove {0}', __('Duplicates')) . ' (strict mode)', ['controller' => 'Logs', 'action' => 'removeDuplicates', '?' => ['strict' => true]]); ?></li>
		<li><?php echo $this->Form->postLink(__('Reset {0}', __('Logs')), ['controller' => 'Logs', 'action' => 'reset'], ['confirm' => 'Sure?']); ?></li>
	</ul>
</nav>

<div class="large-9 medium-8 columns col-lg-9 col-md-8 content">

<h1>Logs</h1>

<?php
if (DatabaseLogsTable::isSearchEnabled()) {
	$typeKeys = array_keys($typesWithCount);
	echo $this->element('DatabaseLog.search', ['url' => ['controller' => 'Logs', 'action' => 'index'], 'types' => array_combine($typeKeys, $typeKeys)]);
}
?>

<ul class="list">
	<li><?php echo $this->Html->link('ALL', ['controller' => 'Logs', 'action' => 'index']); ?></li>
<?php
foreach ($typesWithCount as $type => $count) {
	echo '<li>';
	echo $this->Html->link($this->Log->typeLabel($type), ['controller' => 'Logs', 'action' => 'index', '?' => ['type' => $type]], ['escape' => false]). ' (' . $count . 'x)';
	echo '</li>';
}
?>
</ul>

<?php if ($lastErrors) { ?>
	<h2>Last Errors</h2>
	<ul>
		<?php foreach ($lastErrors as $lastError) { ?>
		<li><?php echo h($lastError['summary']); ?></li>
		<?php } ?>
	</ul>
<?php } ?>

</div>
