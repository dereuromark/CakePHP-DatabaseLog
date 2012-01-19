<?php echo $this->Html->css('/database_logger/css/style'); ?>
<div class="database_logger_plugin">
	<?php echo $this->element('admin_filter', array('plugin' => 'database_logger', 'model' => 'Log')); ?>
	<div class="logs index">
		<h2><?php echo __('Logs');?></h2>
		<table cellpadding="0" cellspacing="0">
		<tr>
				<th><?php echo $this->Paginator->sort('created');?></th>
				<th><?php echo $this->Paginator->sort('type');?></th>
				<th><?php echo $this->Paginator->sort('message');?></th>
				<th class="actions"><?php echo __('Actions');?></th>
		</tr>
		<?php
		$i = 0;
		foreach ($logs as $log):
			$class = null;
			if ($i++ % 2 == 0) {
				$class = ' class="altrow"';
			}
		?>
		<tr<?php echo $class;?>>
			<td><?php echo $this->Time->niceShort($log['Log']['created']); ?>&nbsp;</td>
			<td><?php echo $log['Log']['type']; ?>&nbsp;</td>
			<td><?php echo $log['Log']['message']; ?>&nbsp;</td>
			<td class="actions">
				<?php echo $this->Html->link(__('View Details'), array('action' => 'view', $log['Log']['id'])); ?>
				<?php echo $this->Html->link(__('Delete'), array('action' => 'delete', $log['Log']['id']), null, sprintf(__('Are you sure you want to delete this log # %s?'), $log['Log']['id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
		</table>
		<?php echo $this->element('paging', array('plugin' => 'database_logger')); ?>
	</div>
</div>