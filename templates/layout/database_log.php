<?php
/**
 * DatabaseLog Admin Layout
 *
 * Self-contained admin layout using Bootstrap 5 and Font Awesome 6 via CDN.
 * Completely isolated from host application's CSS/JS.
 *
 * @var \Cake\View\View $this
 */

use Cake\Core\Configure;

$autoRefresh = 0;
$request = $this->getRequest();
if ($request && $request->getParam('controller') === 'DatabaseLog' && $request->getParam('action') === 'index') {
	$autoRefresh = (int)Configure::read('DatabaseLog.dashboardAutoRefresh') ?: 0;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?= $this->fetch('title') ? strip_tags($this->fetch('title')) . ' - ' : '' ?>DatabaseLog Admin</title>

	<!-- Bootstrap 5.3.3 CSS -->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

	<!-- Font Awesome 6.7.2 -->
	<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" rel="stylesheet" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous">

	<style>
		:root {
			--dblog-primary: #0d6efd;
			--dblog-success: #198754;
			--dblog-warning: #ffc107;
			--dblog-danger: #dc3545;
			--dblog-info: #0dcaf0;
			--dblog-secondary: #6c757d;
			--dblog-dark: #212529;
			--dblog-light: #f8f9fa;
			--dblog-sidebar-bg: linear-gradient(135deg, #2c3e50 0%, #1a252f 100%);
			--dblog-sidebar-width: 260px;
		}

		body {
			font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
			background-color: #f4f6f9;
			min-height: 100vh;
		}

		/* Navbar */
		.dblog-navbar {
			background: var(--dblog-dark);
			box-shadow: 0 2px 4px rgba(0,0,0,0.1);
		}

		.dblog-navbar .navbar-brand {
			font-weight: 600;
			color: #fff;
		}

		.dblog-navbar .navbar-brand i {
			color: var(--dblog-primary);
		}

		/* Sidebar */
		.dblog-sidebar {
			background: var(--dblog-sidebar-bg);
			min-height: calc(100vh - 56px);
			width: var(--dblog-sidebar-width);
			position: fixed;
			left: 0;
			top: 56px;
			padding: 1.5rem 0;
			overflow-y: auto;
		}

		.dblog-sidebar .nav-section {
			padding: 0 1rem;
			margin-bottom: 1.5rem;
		}

		.dblog-sidebar .nav-section-title {
			color: rgba(255,255,255,0.5);
			font-size: 0.75rem;
			text-transform: uppercase;
			letter-spacing: 0.05em;
			padding: 0 0.75rem;
			margin-bottom: 0.5rem;
		}

		.dblog-sidebar .nav-link {
			color: rgba(255,255,255,0.8);
			padding: 0.6rem 0.75rem;
			border-radius: 0.375rem;
			margin-bottom: 0.25rem;
			transition: all 0.2s ease;
		}

		.dblog-sidebar .nav-link:hover {
			color: #fff;
			background: rgba(255,255,255,0.1);
		}

		.dblog-sidebar .nav-link.active {
			color: #fff;
			background: var(--dblog-primary);
		}

		.dblog-sidebar .nav-link i {
			width: 1.25rem;
			margin-right: 0.5rem;
		}

		/* Main Content */
		.dblog-main {
			margin-left: var(--dblog-sidebar-width);
			padding: 1.5rem;
			min-height: calc(100vh - 56px);
		}

		/* Stats Cards */
		.stats-card {
			border: none;
			border-radius: 0.5rem;
			box-shadow: 0 2px 4px rgba(0,0,0,0.05);
			transition: transform 0.2s ease, box-shadow 0.2s ease;
			overflow: hidden;
		}

		.stats-card:hover {
			transform: translateY(-2px);
			box-shadow: 0 4px 12px rgba(0,0,0,0.1);
		}

		.stats-card .card-body {
			padding: 1.25rem;
		}

		.stats-card .stats-icon {
			width: 48px;
			height: 48px;
			border-radius: 0.5rem;
			display: flex;
			align-items: center;
			justify-content: center;
			font-size: 1.25rem;
		}

		.stats-card .stats-value {
			font-size: 1.75rem;
			font-weight: 700;
			line-height: 1.2;
		}

		.stats-card .stats-label {
			color: var(--dblog-secondary);
			font-size: 0.875rem;
		}

		/* Tables */
		.table-dblog {
			background: #fff;
			border-radius: 0.5rem;
			overflow: hidden;
			box-shadow: 0 2px 4px rgba(0,0,0,0.05);
		}

		.table-dblog thead th {
			background: var(--dblog-light);
			border-bottom: 2px solid #dee2e6;
			font-weight: 600;
			text-transform: uppercase;
			font-size: 0.75rem;
			letter-spacing: 0.05em;
			color: var(--dblog-secondary);
		}

		.table-dblog tbody tr:hover {
			background-color: rgba(13, 110, 253, 0.05);
		}

		/* Cards */
		.card-dblog {
			border: none;
			border-radius: 0.5rem;
			box-shadow: 0 2px 4px rgba(0,0,0,0.05);
		}

		.card-dblog .card-header {
			background: var(--dblog-light);
			border-bottom: 1px solid #dee2e6;
			font-weight: 600;
		}

		/* Badges */
		.badge-cli {
			background-color: var(--dblog-secondary);
			color: #fff;
		}

		/* Alerts/Flash */
		.dblog-flash {
			border: none;
			border-radius: 0.5rem;
			box-shadow: 0 2px 4px rgba(0,0,0,0.1);
		}

		/* Mobile Navigation */
		.dblog-mobile-nav {
			display: none;
			background: var(--dblog-dark);
			padding: 0.5rem;
		}

		.dblog-mobile-toggle {
			color: #fff;
			background: transparent;
			border: 1px solid rgba(255,255,255,0.2);
			padding: 0.5rem 1rem;
			border-radius: 0.375rem;
		}

		/* Responsive */
		@media (max-width: 991.98px) {
			.dblog-sidebar {
				display: none;
				position: fixed;
				z-index: 1040;
				width: 100%;
				top: 56px;
				left: 0;
				padding-bottom: 2rem;
			}

			.dblog-sidebar.show {
				display: block;
			}

			.dblog-main {
				margin-left: 0;
			}

			.dblog-mobile-nav {
				display: block;
			}
		}

		/* Pre/Code blocks */
		pre {
			background: #f8f9fa;
			border: 1px solid #dee2e6;
			border-radius: 0.375rem;
			padding: 1rem;
			overflow-x: auto;
			white-space: pre-wrap;
			word-wrap: break-word;
			max-width: 100%;
		}

		/* Type filter pills */
		.type-filters .btn {
			margin-right: 0.25rem;
			margin-bottom: 0.25rem;
		}
	</style>

	<?= $this->fetch('css') ?>
</head>
<body>
	<!-- Top Navbar -->
	<nav class="navbar navbar-expand-lg dblog-navbar">
		<div class="container-fluid">
			<a class="navbar-brand" href="<?= $this->Url->build(['plugin' => 'DatabaseLog', 'prefix' => 'Admin', 'controller' => 'DatabaseLog', 'action' => 'index']) ?>">
				<i class="fas fa-database me-2"></i>
				DatabaseLog
			</a>

			<!-- Mobile toggle -->
			<button class="navbar-toggler dblog-mobile-toggle d-lg-none" type="button" onclick="document.querySelector('.dblog-sidebar').classList.toggle('show')">
				<i class="fas fa-bars"></i>
			</button>
		</div>
	</nav>

	<!-- Sidebar -->
	<?= $this->element('DatabaseLog.DatabaseLog/sidebar') ?>

	<!-- Main Content -->
	<main class="dblog-main">
		<!-- Flash Messages -->
		<div class="dblog-flash mb-3">
			<?= $this->element('DatabaseLog.flash/flash') ?>
		</div>

		<?= $this->fetch('content') ?>
	</main>

	<!-- Bootstrap JS -->
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

	<?php if ($autoRefresh > 0): ?>
	<script>
		setTimeout(function() {
			window.location.reload();
		}, <?= $autoRefresh * 1000 ?>);
	</script>
	<?php endif; ?>

	<?= $this->fetch('script') ?>
	<?= $this->fetch('postLink') ?>
</body>
</html>
