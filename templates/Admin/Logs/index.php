<?php
/**
 * @var \App\View\AppView $this
 * @var \DatabaseLog\Model\Entity\DatabaseLog[] $logs
 * @var string|null $currentType
 * @var array $types
 */

use DatabaseLog\Model\Table\DatabaseLogsTable;

?>

<nav class="large-3 medium-4 columns col-lg-3 col-md-4 actions">
	<ul class="side-nav">
		<li class="nav-item heading"><?= __('Actions') ?></li>
		<li><?php echo $this->Html->link(__('Back to Dashboard'), ['controller' => 'DatabaseLog', 'action' => 'index']); ?></li>

		<li><?php echo $this->Form->postLink(__('Remove {0}', __('Duplicates')), ['action' => 'removeDuplicates']); ?></li>
		<?php if ($currentType) { ?>
			<li><?php echo $this->Form->postLink(__('Reset {0}', '"' . $currentType . '" ' . __('Logs')), ['action' => 'reset', '?' => ['type' => $currentType]], ['confirm' => 'Sure?']); ?></li>
		<?php } ?>
		<li><?php echo $this->Form->postLink(__('Reset {0}', __('Logs')), ['action' => 'reset'], ['confirm' => 'Sure?']); ?></li>
	</ul>
</nav>

<div class="large-9 medium-8 columns col-lg-9 col-md-8 content">

<h1><?php echo $currentType ? $this->Log->typeLabel($currentType) : 'All'; ?> Logs</h1>

	<?php
	if (DatabaseLogsTable::isSearchEnabled()) {
		echo $this->element('DatabaseLog.search');
	}
	?>

<ul class="list-inline">
	<li><?php echo $this->Html->link('ALL', ['controller' => 'Logs', 'action' => 'index']); ?></li>
<?php
foreach ($types as $type) {
	echo '<li>';
	echo $this->Html->link($this->Log->typeLabel($type), ['controller' => 'Logs', 'action' => 'index', '?' => ['type' => $type]], ['escape' => false]);
	echo '</li>';
}
?>
</ul>

	<table class="table list">
		<tr>
			<th><?php echo $this->Paginator->sort('created');?></th>
			<th><?php echo $this->Paginator->sort('type');?></th>
			<th><?php echo $this->Paginator->sort('summary');?></th>
			<th class="actions"><?php echo __('Actions');?></th>
		</tr>
		<?php
		foreach ($logs as $log):
			$message = $log->summary;
			$pos = strpos($message, 'Stack Trace:');
			if ($pos) {
				$message = trim(substr($message, 0, $pos));
			}
			$pos = strpos($message, 'Trace:');
			if ($pos) {
				$message = trim(substr($message, 0, $pos));
			}
			?>
			<tr>
				<td><?php echo $this->Time->nice($log->created); ?>&nbsp;</td>
				<td>
					<?php echo $this->Log->typeLabel($log->type); ?>
					<?php
					if ($log->isCli()) {
						echo '<span class="badge badge-secondary label label secondary round radius">cli</span>';
					}

					echo '<div><small>' . $this->Text->truncate($log->uri, 100) . '</small></div>';
					?>

					<?php if ($log->count > 1) { ?>
						<div class="log-count">
							<small>(<?php echo h($log->count); ?>x)</small>
						</div>
					<?php } ?>
				</td>
				<td>
					<?php echo nl2br(h($message)); ?>&nbsp;
				</td>
				<td class="actions">
					<?php echo $this->Html->link(__('Details'), ['action' => 'view', $log->id, '?' => $this->request->getQuery()]); ?>
					<?php echo $this->Form->postLink(__('Delete'), ['action' => 'delete', $log->id], ['confirm' => __('Are you sure you want to delete this log # {0}?', $log->id)]); ?>
				</td>
			</tr>
		<?php endforeach; ?>
	</table>
	<?php echo $this->element('DatabaseLog.paging'); ?>

</div>
