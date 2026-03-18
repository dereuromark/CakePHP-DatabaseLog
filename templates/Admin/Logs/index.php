<?php
/**
 * @var \App\View\AppView $this
 * @var \Cake\ORM\ResultSet<\DatabaseLog\Model\Entity\DatabaseLog> $logs
 * @var string|null $currentType
 * @var array $types
 */

use DatabaseLog\Model\Table\DatabaseLogsTable;

?>

<div class="d-flex justify-content-between align-items-center mb-4">
	<h1 class="mb-0">
		<i class="fas fa-list me-2 text-muted"></i>
		<?= $currentType ? $this->Log->typeLabel($currentType) . ' ' : '' ?><?= __d('database_log', 'Logs') ?>
	</h1>
	<?php if ($currentType): ?>
	<div>
		<?= $this->Form->postLink(
			'<i class="fas fa-trash me-1"></i>' . __d('database_log', 'Reset {0} Logs', '"' . $currentType . '"'),
			['action' => 'reset', '?' => ['type' => $currentType]],
			[
				'class' => 'btn btn-outline-danger btn-sm',
				'escapeTitle' => false,
				'confirm' => __d('database_log', 'Delete all {0} logs? This cannot be undone.', $currentType),
				'block' => true,
			]
		) ?>
	</div>
	<?php endif; ?>
</div>

<!-- Type Filters -->
<div class="type-filters mb-3">
	<a href="<?= $this->Url->build(['action' => 'index']) ?>" class="btn btn-<?= $currentType === null ? 'primary' : 'outline-secondary' ?> btn-sm">
		<?= __d('database_log', 'All') ?>
	</a>
	<?php foreach ($types as $type): ?>
	<a href="<?= $this->Url->build(['action' => 'index', '?' => ['type' => $type]]) ?>" class="btn btn-<?= $currentType === $type ? 'primary' : 'outline-secondary' ?> btn-sm">
		<?= $this->Log->typeLabel($type) ?>
	</a>
	<?php endforeach; ?>
</div>

<!-- Search -->
<?php if (DatabaseLogsTable::isSearchEnabled()): ?>
<div class="card card-dblog mb-3">
	<div class="card-body">
		<?= $this->element('DatabaseLog.search') ?>
	</div>
</div>
<?php endif; ?>

<!-- Logs Table -->
<div class="card card-dblog">
	<div class="table-responsive">
		<table class="table table-dblog table-hover mb-0">
			<thead>
				<tr>
					<th style="width: 160px;"><?= $this->Paginator->sort('created', __d('database_log', 'Created')) ?></th>
					<th style="width: 120px;"><?= $this->Paginator->sort('type', __d('database_log', 'Type')) ?></th>
					<th><?= $this->Paginator->sort('summary', __d('database_log', 'Summary')) ?></th>
					<th style="width: 120px;" class="text-end"><?= __d('database_log', 'Actions') ?></th>
				</tr>
			</thead>
			<tbody>
				<?php if ($logs->items()->isEmpty()): ?>
				<tr>
					<td colspan="4" class="text-center text-muted py-4">
						<i class="fas fa-inbox fa-2x mb-2 d-block"></i>
						<?= __d('database_log', 'No log entries found.') ?>
					</td>
				</tr>
				<?php else: ?>
				<?php foreach ($logs as $log): ?>
				<?php
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
					<td>
						<small><?= $this->Time->nice($log->created) ?></small>
					</td>
					<td>
						<?= $this->Log->typeLabel($log->type) ?>
						<?php if ($log->isCli()): ?>
						<span class="badge bg-secondary ms-1">cli</span>
						<?php endif; ?>
						<?php if ($log->count > 1): ?>
						<span class="badge bg-info ms-1"><?= h($log->count) ?>x</span>
						<?php endif; ?>
						<?php if ($log->uri): ?>
						<div><small class="text-muted"><?= $this->Text->truncate(h($log->uri), 40) ?></small></div>
						<?php endif; ?>
					</td>
					<td>
						<span class="text-break"><?= nl2br(h($this->Text->truncate($message, 200))) ?></span>
					</td>
					<td class="text-end">
						<div class="btn-group btn-group-sm">
							<a href="<?= $this->Url->build(['action' => 'view', $log->id, '?' => $this->request->getQuery()]) ?>" class="btn btn-outline-primary" title="<?= __d('database_log', 'View') ?>">
								<i class="fas fa-eye"></i>
							</a>
							<?= $this->Form->postLink(
								'<i class="fas fa-trash"></i>',
								['action' => 'delete', $log->id],
								[
									'class' => 'btn btn-outline-danger',
									'escapeTitle' => false,
									'confirm' => __d('database_log', 'Delete log #{0}?', $log->id),
									'title' => __d('database_log', 'Delete'),
									'block' => true,
								]
							) ?>
						</div>
					</td>
				</tr>
				<?php endforeach; ?>
				<?php endif; ?>
			</tbody>
		</table>
	</div>
</div>

<!-- Pagination -->
<?= $this->element('DatabaseLog.paging') ?>
