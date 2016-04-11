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
	<div class="logs view">
	<h1><?php echo __('Log');?></h1>
		<dl>
			<dt><?php echo __('type'); ?></dt>
			<dd>
				<?php echo h($log['type']); ?>
			</dd>
			<dt><?php echo __('Message'); ?></dt>
			<dd>
				<?php echo trim(nl2br(h($log['message']))); ?>
			</dd>
			<dt><?php echo __('Context'); ?></dt>
			<dd>
				<?php echo nl2br(h($log['context'])); ?>
				&nbsp;
			</dd>
			<dt><?php echo __('Uri'); ?></dt>
			<dd>
				<?php echo h($log['uri']); ?>
			</dd>
			<dt><?php echo __('Referrer'); ?></dt>
			<dd>
				<?php echo h($log['refer']); ?>
			</dd>
			<dt><?php echo __('Hostname'); ?></dt>
			<dd>
				<?php echo h($log['hostname']); ?>
			</dd>
			<dt><?php echo __('IP'); ?></dt>
			<dd>
				<?php echo h($log['ip']); ?>
			</dd>
			<dt><?php echo __('Created'); ?></dt>
			<dd>
				<?php echo $this->Time->nice($log['created']); ?>
			</dd>
		</dl>
	</div>

	<div class="actions">
		<ul>
			<li><?php echo $this->Form->postLink(__('Delete {0}', __('Log Entry')), ['action' => 'delete', $log['id']], ['confirm' => __('Are you sure?')]); ?></li>
			<li><?php echo $this->Html->link(__('Back'), ['action' => 'index', '?' => $this->request->query]); ?></li>
		</ul>
	</div>
</div>
