<?php
/**
 * Stats Card Element
 *
 * @var \Cake\View\View $this
 * @var string $label The card label
 * @var int|string $value The stat value
 * @var string $icon Font Awesome icon class (e.g., 'fa-exclamation-circle')
 * @var string $color Bootstrap color (primary, success, warning, danger, info, secondary)
 * @var string|null $link Optional link URL
 */

$color = $color ?? 'primary';
$icon = $icon ?? 'fa-chart-bar';
$link = $link ?? null;

$bgOpacity = [
	'danger' => 'bg-danger bg-opacity-10',
	'warning' => 'bg-warning bg-opacity-10',
	'success' => 'bg-success bg-opacity-10',
	'info' => 'bg-info bg-opacity-10',
	'primary' => 'bg-primary bg-opacity-10',
	'secondary' => 'bg-secondary bg-opacity-10',
];

$textColor = [
	'danger' => 'text-danger',
	'warning' => 'text-warning',
	'success' => 'text-success',
	'info' => 'text-info',
	'primary' => 'text-primary',
	'secondary' => 'text-secondary',
];
?>
<?php if ($link): ?>
<a href="<?= h($link) ?>" class="text-decoration-none">
<?php endif; ?>
<div class="card stats-card h-100">
	<div class="card-body">
		<div class="d-flex align-items-center">
			<div class="stats-icon <?= $bgOpacity[$color] ?? $bgOpacity['primary'] ?> <?= $textColor[$color] ?? $textColor['primary'] ?>">
				<i class="fas <?= h($icon) ?>"></i>
			</div>
			<div class="ms-3 flex-grow-1">
				<div class="stats-value"><?= h($value) ?></div>
				<div class="stats-label"><?= h($label) ?></div>
			</div>
		</div>
	</div>
</div>
<?php if ($link): ?>
</a>
<?php endif; ?>
