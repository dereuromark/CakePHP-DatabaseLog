<?php
/**
 * @var \App\View\AppView $this
 * @var \DatabaseLog\Model\Entity\DatabaseLog $log
 */
?>
<div class="database-log-plugin row">

<div class="col-xs-12 view">
	<h1><?php echo __('Log');?></h1>

<div style="float: right">
	<?php echo $this->Html->link(__('Formatted'), [$log['id'], '?' => ['formatted' => true] + $this->request->getQuery()], ['class' => 'btn btn-default']); ?>
</div>

		<dl>
			<dt><?php echo __('type'); ?></dt>
			<dd>
				<?php echo $this->Log->typeLabel($log['type']); ?>
				<?php
				$isCli = $log->isCli();
				if ($isCli) {
					echo '<span class="badge label label secondary round radius">cli</span>';
				}
				?>
			</dd>
			<dt><?php echo __('Message'); ?></dt>
			<dd>
				<?php if ($this->request->getQuery('formatted')) {
					echo '<pre>' . trim(h($log['message'])) . '</pre>';
				} else {
					echo trim(nl2br(h($log['message'])));
				} ?>
			</dd>
			<dt><?php echo __('Context'); ?></dt>
			<dd>
				<?php if ($this->request->getQuery('formatted')) {
					echo '<pre>' . trim(h($log['context'])) . '</pre>';
				} else {
					echo trim(nl2br(h($log['context'])));
				} ?>
			</dd>
			<dt><?php echo $isCli ? __('Command') :  __('Uri'); ?></dt>
			<dd>
				<?php echo h($log['uri']); ?>
			</dd>

			<?php if (!$isCli) { ?>
			<dt><?php echo __('Referrer'); ?></dt>
			<dd>
				<?php echo h($log['refer']); ?>
			</dd>
			<?php } ?>

			<dt><?php echo __('Hostname'); ?></dt>
			<dd>
				<?php echo h($log['hostname']); ?>
			</dd>
			<dt><?php echo __('IP'); ?></dt>
			<dd>
				<?php echo h($log['ip']); ?>
			</dd>

			<dt><?php echo __('User Agent'); ?></dt>
			<dd>
				<?php echo h($log->user_agent); ?>
			</dd>

			<dt><?php echo __('Created'); ?></dt>
			<dd>
				<?php echo $this->Time->nice($log['created']); ?>
			</dd>
		</dl>
	</div>

	<div class="actions col-xs-12">
	<ul>
		<li><?php echo $this->Form->postLink(__('Delete {0}', __('Log Entry')), ['action' => 'delete', $log['id']], ['confirm' => __('Are you sure?')]); ?></li>
		<li><?php echo $this->Html->link(__('Back'), ['action' => 'index', '?' => $this->request->getQuery()]); ?></li>
	</ul>
	</div>

</div>
