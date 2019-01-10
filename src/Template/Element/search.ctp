<?php
/**
 * @var \App\View\AppView $this
 */
?>
<div class="databaselog-search-logs" style="float: right">
	<?php
	echo $this->Form->create(null, ['valueSources' => 'query']);
	echo $this->Form->control('search', ['placeholder' => 'Auto-wildcard mode', 'label' => 'Search (Message)']);
	echo $this->Form->control('type', ['empty' => ' - no filter - ']);
	echo $this->Form->button('Filter', ['type' => 'submit']);
	if (!empty($_isSearch)) {
		echo $this->Html->link('Reset', ['action' => 'index'], ['class' => 'button']);
	}
	echo $this->Form->end();
	?>
</div>
