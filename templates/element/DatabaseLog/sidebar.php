<?php
/**
 * DatabaseLog Admin Sidebar Navigation
 *
 * @var \Cake\View\View $this
 */

$controller = $this->getRequest()->getParam('controller');
$action = $this->getRequest()->getParam('action');

$isActive = function (string $c, ?array $actions = null) use ($controller, $action): string {
	if ($controller !== $c) {
		return '';
	}
	if ($actions === null) {
		return 'active';
	}

	return in_array($action, $actions, true) ? 'active' : '';
};
?>
<aside class="dblog-sidebar d-none d-lg-block">
	<!-- Navigation -->
	<div class="nav-section">
		<div class="nav-section-title"><?= __d('database_log', 'Navigation') ?></div>
		<nav class="nav flex-column">
			<a class="nav-link <?= $isActive('DatabaseLog', ['index']) ?>" href="<?= $this->Url->build(['plugin' => 'DatabaseLog', 'prefix' => 'Admin', 'controller' => 'DatabaseLog', 'action' => 'index']) ?>">
				<i class="fas fa-tachometer-alt"></i>
				<?= __d('database_log', 'Dashboard') ?>
			</a>
			<a class="nav-link <?= $isActive('Logs') ?>" href="<?= $this->Url->build(['plugin' => 'DatabaseLog', 'prefix' => 'Admin', 'controller' => 'Logs', 'action' => 'index']) ?>">
				<i class="fas fa-list"></i>
				<?= __d('database_log', 'Logs') ?>
			</a>
		</nav>
	</div>

	<!-- Quick Actions -->
	<div class="nav-section">
		<div class="nav-section-title"><?= __d('database_log', 'Quick Actions') ?></div>
		<nav class="nav flex-column">
			<?= $this->Form->postLink(
				'<i class="fas fa-compress-alt"></i> ' . __d('database_log', 'Remove Duplicates'),
				['plugin' => 'DatabaseLog', 'prefix' => 'Admin', 'controller' => 'Logs', 'action' => 'removeDuplicates'],
				[
					'class' => 'nav-link',
					'escapeTitle' => false,
					'confirm' => __d('database_log', 'Remove all duplicate log entries?'),
					'block' => true,
				]
			) ?>
			<?= $this->Form->postLink(
				'<i class="fas fa-compress"></i> ' . __d('database_log', 'Remove Duplicates (strict)'),
				['plugin' => 'DatabaseLog', 'prefix' => 'Admin', 'controller' => 'Logs', 'action' => 'removeDuplicates', '?' => ['strict' => true]],
				[
					'class' => 'nav-link',
					'escapeTitle' => false,
					'confirm' => __d('database_log', 'Remove all duplicate log entries (strict mode)?'),
					'block' => true,
				]
			) ?>
			<?= $this->Form->postLink(
				'<i class="fas fa-trash"></i> ' . __d('database_log', 'Reset All Logs'),
				['plugin' => 'DatabaseLog', 'prefix' => 'Admin', 'controller' => 'Logs', 'action' => 'reset'],
				[
					'class' => 'nav-link text-warning',
					'escapeTitle' => false,
					'confirm' => __d('database_log', 'Delete all log entries? This cannot be undone.'),
					'block' => true,
				]
			) ?>
		</nav>
	</div>
</aside>
