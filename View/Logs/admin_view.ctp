<?php //echo $this->Html->css('/database_log/css/style'); ?>
<div class="database_log_plugin">
	<div class="logs view">
	<h2><?php echo __('Log');?></h2>
		<dl><?php $i = 0; $class = ' class="altrow"';?>
			<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('type'); ?></dt>
			<dd<?php if ($i++ % 2 == 0) echo $class;?>>
				<?php echo $log['Log']['type']; ?>
				&nbsp;
			</dd>
			<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Message'); ?></dt>
			<dd<?php if ($i++ % 2 == 0) echo $class;?>>
				<?php echo nl2br(h($log['Log']['message'])); ?>
				&nbsp;
			</dd>
			<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Uri'); ?></dt>
			<dd<?php if ($i++ % 2 == 0) echo $class;?>>
				<?php echo h($log['Log']['uri']); ?>
				&nbsp;
			</dd>
			<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Referrer'); ?></dt>
			<dd<?php if ($i++ % 2 == 0) echo $class;?>>
				<?php echo h($log['Log']['refer']); ?>
				&nbsp;
			</dd>
			<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Hostname'); ?></dt>
			<dd<?php if ($i++ % 2 == 0) echo $class;?>>
				<?php echo h($log['Log']['hostname']); ?>
				&nbsp;
			</dd>
			<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('IP'); ?></dt>
			<dd<?php if ($i++ % 2 == 0) echo $class;?>>
				<?php echo $log['Log']['ip']; ?>
				&nbsp;
			</dd>
			<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Created'); ?></dt>
			<dd<?php if ($i++ % 2 == 0) echo $class;?>>
				<?php echo $log['Log']['created']; ?>
				&nbsp;
			</dd>
		</dl>
	</div>

	<div class="actions">
		<ul>
			<li><?php echo $this->Form->postLink(__('Delete %s', __('Log')), array('action' => 'delete', $log['Log']['id']), array(), __('Are you sure?')); ?></li>
			<li><?php echo $this->Html->link(__('List %s', __('Logs')), array('action' => 'index')); ?></li>
		</ul>
	</div>
</div>