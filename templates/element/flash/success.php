<?php
/**
 * @var \Cake\View\View $this
 * @var array $params
 * @var string $message
 */

$class = 'alert alert-success alert-dismissible fade show dblog-flash';
if (!empty($params['class'])) {
	$class .= ' ' . $params['class'];
}
if (!isset($params['escape']) || $params['escape'] !== false) {
	$message = h($message);
}
?>
<div class="<?= $class ?>" role="alert">
	<i class="fas fa-check-circle me-2"></i>
	<?= $message ?>
	<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
