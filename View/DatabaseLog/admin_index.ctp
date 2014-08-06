<ul>
<?php
foreach ($types as $type) {
	echo '<li>';
	echo $this->Html->link($type, array('controller' => 'logs', 'action' => 'index', '?' => array('type' => $type)));
	echo '</li>';
}
?>
</ul>