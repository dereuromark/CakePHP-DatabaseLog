<?php
/**
 * @var \App\View\AppView $this
 * @var array $types
 * @var mixed $_isSearch
 * @var mixed $url
 */
?>
<div class="databaselog-search-logs" style="float: right">
	<?php
	echo $this->Form->create(null, ['valueSources' => 'query', 'url' => isset($url) ? $url : null]);
	echo $this->Form->control('search', ['placeholder' => 'Auto-wildcard mode', 'label' => 'Search (Summary)']);

	echo $this->Form->control('type', ['empty' => ' - no filter - ', 'options' => $types]);
	echo $this->Form->button('Filter', ['type' => 'submit']);
	if (!empty($_isSearch)) {
		echo $this->Html->link('Reset', ['action' => 'index'], ['class' => 'button']);
	}
	echo $this->Form->end();
	?>
</div>
