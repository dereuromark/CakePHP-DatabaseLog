<?php
/**
 * @var \App\View\AppView $this
 * @var array<string, int> $typesWithCount
 * @var array $lastErrors
 * @var string $databaseType
 * @var int|null $databaseSize
 */

$typeColors = [
	'error' => 'danger',
	'warning' => 'warning',
	'notice' => 'warning',
	'info' => 'info',
	'debug' => 'secondary',
];

$typeIcons = [
	'error' => 'fa-exclamation-circle',
	'warning' => 'fa-exclamation-triangle',
	'notice' => 'fa-flag',
	'info' => 'fa-info-circle',
	'debug' => 'fa-bug',
];
?>

<h1 class="mb-4">
	<i class="fas fa-tachometer-alt me-2 text-muted"></i>
	<?= __d('database_log', 'Dashboard') ?>
</h1>

<!-- Database Info Card -->
<div class="row mb-4">
	<div class="col-12">
		<div class="card card-dblog">
			<div class="card-body d-flex align-items-center">
				<div class="stats-icon bg-primary bg-opacity-10 text-primary me-3">
					<i class="fas fa-database"></i>
				</div>
				<div>
					<h5 class="mb-0"><?= h($databaseType) ?></h5>
					<?php if ($databaseSize !== null): ?>
					<small class="text-muted"><?= $this->Number->toReadableSize($databaseSize) ?></small>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Stats Cards -->
<?php if ($typesWithCount): ?>
<div class="row g-3 mb-4">
	<?php foreach ($typesWithCount as $type => $count): ?>
	<div class="col-6 col-md-4 col-lg-3">
		<?= $this->element('DatabaseLog.DatabaseLog/stats_card', [
			'label' => ucfirst($type),
			'value' => $count,
			'icon' => $typeIcons[$type] ?? 'fa-file-alt',
			'color' => $typeColors[$type] ?? 'secondary',
			'link' => $this->Url->build(['controller' => 'Logs', 'action' => 'index', '?' => ['type' => $type]]),
		]) ?>
	</div>
	<?php endforeach; ?>
</div>
<?php else: ?>
<div class="alert alert-info">
	<i class="fas fa-info-circle me-2"></i>
	<?= __d('database_log', 'No log entries found.') ?>
</div>
<?php endif; ?>

<!-- Last Errors -->
<?php if ($lastErrors): ?>
<div class="card card-dblog">
	<div class="card-header">
		<i class="fas fa-exclamation-circle text-danger me-2"></i>
		<?= __d('database_log', 'Last Errors') ?>
	</div>
	<div class="card-body p-0">
		<ul class="list-group list-group-flush">
			<?php foreach ($lastErrors as $lastError): ?>
			<li class="list-group-item d-flex justify-content-between align-items-center">
				<code class="text-danger text-truncate me-2"><?= h($lastError['summary']) ?></code>
				<a href="<?= $this->Url->build(['controller' => 'Logs', 'action' => 'view', $lastError['id']]) ?>" class="btn btn-outline-primary btn-sm flex-shrink-0">
					<i class="fas fa-eye"></i>
				</a>
			</li>
			<?php endforeach; ?>
		</ul>
	</div>
	<div class="card-footer bg-transparent">
		<a href="<?= $this->Url->build(['controller' => 'Logs', 'action' => 'index', '?' => ['type' => 'error']]) ?>" class="btn btn-outline-danger btn-sm">
			<i class="fas fa-list me-1"></i>
			<?= __d('database_log', 'View All Errors') ?>
		</a>
	</div>
</div>
<?php endif; ?>
