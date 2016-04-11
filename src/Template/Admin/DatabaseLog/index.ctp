<?php
/**
 * CakePHP DatabaseLog Plugin
 *
 * Licensed under The MIT License.
 *
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 * @link https://github.com/dereuromark/CakePHP-DatabaseLog
 */
?>
<ul>
<?php
foreach ($types as $type) {
	echo '<li>';
	echo $this->Html->link($type, array('controller' => 'Logs', 'action' => 'index', '?' => array('type' => $type)));
	echo '</li>';
}
?>
</ul>