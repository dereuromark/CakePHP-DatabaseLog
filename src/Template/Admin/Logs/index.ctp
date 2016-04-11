<?php
/**
 * CakePHP DatabaseLog Plugin
 *
 * Licensed under The MIT License.
 *
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 * @link https://github.com/dereuromark/CakePHP-DatabaseLog
 */
?>
<div class="database-log-plugin">

<div class="logs index">
<h1>Logs</h1>

<ul class="list-inline">
	<li><?php echo $this->Html->link('ALL', ['controller' => 'Logs', 'action' => 'index']); ?></li>
<?php
foreach ($types as $type) {
	echo '<li>';
	echo $this->Html->link($type, ['controller' => 'Logs', 'action' => 'index', '?' => ['type' => $type]]);
	echo '</li>';
}
?>
</ul>

<?php echo $this->element('DatabaseLog.admin_filter'); ?>

	<table class="table list">
		<tr>
			<th><?php echo $this->Paginator->sort('created');?></th>
			<th><?php echo $this->Paginator->sort('type');?></th>
			<th><?php echo $this->Paginator->sort('message');?></th>
			<th class="actions"><?php echo __('Actions');?></th>
		</tr>
		<?php
		foreach ($logs as $log):
			$message = $log['message'];
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
				<td><?php echo $this->Time->nice($log['created']); ?>&nbsp;</td>
				<td><?php echo h($log['type']); ?>&nbsp;</td>
				<td><?php echo nl2br(h($message)); ?>&nbsp;</td>
				<td class="actions">
					<?php echo $this->Html->link(__('Details'), ['action' => 'view', $log['id'], '?' => $this->request->query]); ?>
					<?php echo $this->Form->postLink(__('Delete'), ['action' => 'delete', $log['id']], ['confirm' => __('Are you sure you want to delete this log # {0}?', $log['id'])]); ?>
				</td>
			</tr>
		<?php endforeach; ?>
	</table>
	<?php echo $this->element('DatabaseLog.paging'); ?>

</div>
<div class="actions">
	<ul>
		<li><?php echo $this->Form->postLink(__('Remove {0}', __('Duplicates')), ['action' => 'removeDuplicates']); ?></li>
		<li><?php echo $this->Form->postLink(__('Reset {0}', __('Logs')), ['action' => 'reset'], ['confirm' => 'Sure?']); ?></li>
	</ul>
</div>

</div>
