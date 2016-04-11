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
<p>
<?php
echo $this->Paginator->counter();
if (isset($filter)) {
	$this->Paginator->options(['url' => [$filter]]);
}
?>	</p>

<div class="pagination-container">
	<?php echo $this->element('Tools.pagination'); ?>
</div>
