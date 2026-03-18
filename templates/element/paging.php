<?php
/**
 * CakePHP DatabaseLog Plugin
 *
 * Licensed under The MIT License.
 *
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 * @link https://github.com/dereuromark/CakePHP-DatabaseLog
 * @var \App\View\AppView $this
 */

use Cake\Core\Plugin;

if (Plugin::isLoaded('Tools')) {
	echo $this->element('Tools.pagination');
} else {
?>
<nav class="mt-3" aria-label="<?= __d('database_log', 'Page navigation') ?>">
	<ul class="pagination justify-content-center mb-2">
		<?= $this->Paginator->first('<i class="fas fa-angle-double-left"></i>', ['escape' => false, 'class' => 'page-link']) ?>
		<?= $this->Paginator->prev('<i class="fas fa-angle-left"></i>', ['escape' => false, 'class' => 'page-link']) ?>
		<?= $this->Paginator->numbers(['class' => 'page-link']) ?>
		<?= $this->Paginator->next('<i class="fas fa-angle-right"></i>', ['escape' => false, 'class' => 'page-link']) ?>
		<?= $this->Paginator->last('<i class="fas fa-angle-double-right"></i>', ['escape' => false, 'class' => 'page-link']) ?>
	</ul>
	<p class="text-center text-muted small">
		<?= $this->Paginator->counter(__d('database_log', 'Page {{page}} of {{pages}}, showing {{current}} record(s) out of {{count}} total')) ?>
	</p>
</nav>
<?php
}
