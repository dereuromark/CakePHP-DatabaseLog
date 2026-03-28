<?php
/**
 * @var \App\View\AppView $this
 * @var array<string, int> $typesWithCount
 * @var array $lastErrors
 * @var string $databaseType
 * @var int|null $databaseSize
 * @var array $stats
 * @var string $period
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

// Chart.js colors for log types
$chartColors = [
	'error' => ['bg' => 'rgba(220, 53, 69, 0.2)', 'border' => 'rgb(220, 53, 69)'],
	'warning' => ['bg' => 'rgba(255, 193, 7, 0.2)', 'border' => 'rgb(255, 193, 7)'],
	'notice' => ['bg' => 'rgba(255, 193, 7, 0.15)', 'border' => 'rgb(230, 173, 0)'],
	'info' => ['bg' => 'rgba(13, 202, 240, 0.2)', 'border' => 'rgb(13, 202, 240)'],
	'debug' => ['bg' => 'rgba(108, 117, 125, 0.2)', 'border' => 'rgb(108, 117, 125)'],
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
					<h5 class="mb-0"><?= __d('database_log', 'Logs Table') ?></h5>
					<small class="text-muted">
						<?= h($databaseType) ?>
						<?php if ($databaseSize !== null) { ?>
						- <?= $this->Number->toReadableSize($databaseSize) ?>
					<?php } ?>
					</small>
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

<!-- Time-based Statistics Chart -->
<div class="card card-dblog mb-4">
	<div class="card-header d-flex justify-content-between align-items-center">
		<span>
			<i class="fas fa-chart-line me-2"></i>
			<?= __d('database_log', 'Log Activity') ?>
		</span>
		<div class="btn-group btn-group-sm">
			<a href="<?= $this->Url->build(['action' => 'index', '?' => ['period' => '24h']]) ?>" class="btn btn-<?= $period === '24h' ? 'primary' : 'outline-secondary' ?>">
				<?= __d('database_log', '24h') ?>
			</a>
			<a href="<?= $this->Url->build(['action' => 'index', '?' => ['period' => '7d']]) ?>" class="btn btn-<?= $period === '7d' ? 'primary' : 'outline-secondary' ?>">
				<?= __d('database_log', '7 days') ?>
			</a>
			<a href="<?= $this->Url->build(['action' => 'index', '?' => ['period' => '30d']]) ?>" class="btn btn-<?= $period === '30d' ? 'primary' : 'outline-secondary' ?>">
				<?= __d('database_log', '30 days') ?>
			</a>
		</div>
	</div>
	<div class="card-body">
		<?php if (!empty($stats['datasets'])): ?>
		<canvas id="logActivityChart" height="100"></canvas>
		<?php else: ?>
		<p class="text-muted text-center mb-0">
			<i class="fas fa-chart-line fa-2x mb-2 d-block opacity-50"></i>
			<?= __d('database_log', 'No data for this period.') ?>
		</p>
		<?php endif; ?>
	</div>
</div>

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

<?php if (!empty($stats['datasets'])): ?>
<?php $this->append('script'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
	var ctx = document.getElementById('logActivityChart').getContext('2d');
	var chartData = <?= json_encode($stats) ?>;
	var chartColors = <?= json_encode($chartColors) ?>;

	var datasets = [];
	for (var type in chartData.datasets) {
		var color = chartColors[type] || {bg: 'rgba(108, 117, 125, 0.2)', border: 'rgb(108, 117, 125)'};
		datasets.push({
			label: type.charAt(0).toUpperCase() + type.slice(1),
			data: Object.values(chartData.datasets[type]),
			backgroundColor: color.bg,
			borderColor: color.border,
			borderWidth: 2,
			fill: true,
			tension: 0.3
		});
	}

	new Chart(ctx, {
		type: 'line',
		data: {
			labels: chartData.labels,
			datasets: datasets
		},
		options: {
			responsive: true,
			maintainAspectRatio: true,
			interaction: {
				intersect: false,
				mode: 'index'
			},
			plugins: {
				legend: {
					position: 'bottom'
				}
			},
			scales: {
				y: {
					beginAtZero: true,
					ticks: {
						stepSize: 1
					}
				}
			}
		}
	});
});
</script>
<?php $this->end(); ?>
<?php endif; ?>
