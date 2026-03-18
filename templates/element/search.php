<?php
/**
 * @var \App\View\AppView $this
 * @var array $types
 * @var mixed $_isSearch
 * @var mixed $url
 */
?>
<?= $this->Form->create(null, ['valueSources' => 'query', 'url' => $url ?? null, 'type' => 'get', 'class' => 'row g-2 align-items-end']) ?>
<div class="col-md-5">
	<?= $this->Form->control('search', [
		'placeholder' => __d('database_log', 'Auto-wildcard mode'),
		'label' => __d('database_log', 'Search (Summary)'),
		'class' => 'form-control',
	]) ?>
</div>
<div class="col-md-4">
	<?= $this->Form->control('type', [
		'empty' => __d('database_log', '- no filter -'),
		'options' => $types ?? [],
		'label' => __d('database_log', 'Type'),
		'class' => 'form-select',
	]) ?>
</div>
<div class="col-md-3">
	<button type="submit" class="btn btn-primary w-100">
		<i class="fas fa-search me-1"></i>
		<?= __d('database_log', 'Filter') ?>
	</button>
</div>
<?= $this->Form->end() ?>
<?php if (!empty($_isSearch)): ?>
<div class="mt-2">
	<a href="<?= $this->Url->build(['action' => 'index']) ?>" class="btn btn-outline-secondary btn-sm">
		<i class="fas fa-times me-1"></i>
		<?= __d('database_log', 'Reset') ?>
	</a>
</div>
<?php endif; ?>
