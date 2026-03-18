<?php
/**
 * @var \App\View\AppView $this
 * @var \DatabaseLog\Model\Entity\DatabaseLog $log
 */

$isCli = $log->isCli();
$formatted = $this->request->getQuery('formatted');
?>

<div class="d-flex justify-content-between align-items-center mb-4">
	<h1 class="mb-0">
		<i class="fas fa-file-alt me-2 text-muted"></i>
		<?= __d('database_log', 'Log') ?> #<?= h($log->id) ?>
	</h1>
	<div>
		<?php if ($formatted): ?>
		<a href="<?= $this->Url->build([$log->id, '?' => array_diff_key($this->request->getQuery(), ['formatted' => true])]) ?>" class="btn btn-outline-secondary btn-sm">
			<i class="fas fa-align-left me-1"></i>
			<?= __d('database_log', 'Normal') ?>
		</a>
		<?php else: ?>
		<a href="<?= $this->Url->build([$log->id, '?' => ['formatted' => true] + $this->request->getQuery()]) ?>" class="btn btn-outline-secondary btn-sm">
			<i class="fas fa-code me-1"></i>
			<?= __d('database_log', 'Formatted') ?>
		</a>
		<?php endif; ?>
		<a href="<?= $this->Url->build(['action' => 'index', '?' => $this->request->getQuery()]) ?>" class="btn btn-outline-primary btn-sm">
			<i class="fas fa-arrow-left me-1"></i>
			<?= __d('database_log', 'Back') ?>
		</a>
		<?= $this->Form->postLink(
			'<i class="fas fa-trash me-1"></i>' . __d('database_log', 'Delete'),
			['action' => 'delete', $log->id],
			[
				'class' => 'btn btn-outline-danger btn-sm',
				'escapeTitle' => false,
				'confirm' => __d('database_log', 'Delete this log entry?'),
				'block' => true,
			]
		) ?>
	</div>
</div>

<div class="card card-dblog mb-4">
	<div class="card-header">
		<i class="fas fa-info-circle me-2"></i>
		<?= __d('database_log', 'Details') ?>
	</div>
	<div class="card-body">
		<dl class="row mb-0">
			<dt class="col-sm-3"><?= __d('database_log', 'Type') ?></dt>
			<dd class="col-sm-9">
				<?= $this->Log->typeLabel($log->type) ?>
				<?php if ($isCli): ?>
				<span class="badge bg-secondary ms-1">cli</span>
				<?php endif; ?>
			</dd>

			<dt class="col-sm-3"><?= __d('database_log', 'Created') ?></dt>
			<dd class="col-sm-9"><?= $this->Time->nice($log->created) ?></dd>

			<dt class="col-sm-3"><?= $isCli ? __d('database_log', 'Command') : __d('database_log', 'URI') ?></dt>
			<dd class="col-sm-9"><?= h($log->uri) ?: '-' ?></dd>

			<?php if (!$isCli): ?>
			<dt class="col-sm-3"><?= __d('database_log', 'Referrer') ?></dt>
			<dd class="col-sm-9"><?= h($log->refer) ?: '-' ?></dd>
			<?php endif; ?>

			<dt class="col-sm-3"><?= __d('database_log', 'Hostname') ?></dt>
			<dd class="col-sm-9"><?= h($log->hostname) ?: '-' ?></dd>

			<dt class="col-sm-3"><?= __d('database_log', 'IP') ?></dt>
			<dd class="col-sm-9"><?= h($log->ip) ?: '-' ?></dd>

			<dt class="col-sm-3"><?= __d('database_log', 'User Agent') ?></dt>
			<dd class="col-sm-9"><small><?= h($log->user_agent) ?: '-' ?></small></dd>
		</dl>
	</div>
</div>

<?php if ($log->summary && !$log->message): ?>
<div class="card card-dblog mb-4">
	<div class="card-header">
		<i class="fas fa-align-left me-2"></i>
		<?= __d('database_log', 'Summary') ?>
	</div>
	<div class="card-body">
		<?php if ($formatted): ?>
		<pre class="mb-0"><?= trim(h($log->summary)) ?></pre>
		<?php else: ?>
		<p class="mb-0"><?= trim(nl2br(h($log->summary))) ?></p>
		<?php endif; ?>
	</div>
</div>
<?php endif; ?>

<div class="card card-dblog mb-4">
	<div class="card-header">
		<i class="fas fa-envelope me-2"></i>
		<?= __d('database_log', 'Message') ?>
	</div>
	<div class="card-body">
		<?php if ($formatted): ?>
		<pre class="mb-0"><?= trim(h($log->message)) ?></pre>
		<?php else: ?>
		<p class="mb-0"><?= trim(nl2br(h($log->message))) ?></p>
		<?php endif; ?>
	</div>
</div>

<?php if ($log->context): ?>
<div class="card card-dblog">
	<div class="card-header">
		<i class="fas fa-code me-2"></i>
		<?= __d('database_log', 'Context') ?>
	</div>
	<div class="card-body">
		<?php if ($formatted): ?>
		<pre class="mb-0"><?= trim(h($log->context)) ?></pre>
		<?php else: ?>
		<p class="mb-0"><?= trim(nl2br(h($log->context))) ?></p>
		<?php endif; ?>
	</div>
</div>
<?php endif; ?>
