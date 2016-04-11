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
echo $this->Paginator->counter(array(
'format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%')
));
if (isset($filter)) {
	$this->Paginator->options(array('url' => array($filter)));
}
?>	</p>

<div class="pagination-container">
		<?php echo $this->element('Tools.pagination'); ?>
	</div>