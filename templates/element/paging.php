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
	$this->Paginator->setTemplates([
		'nextActive' => '<li class="page-item"><a class="page-link" rel="next" href="{{url}}">{{text}}</a></li>',
		'nextDisabled' => '<li class="page-item disabled"><a class="page-link" href="" onclick="return false;">{{text}}</a></li>',
		'prevActive' => '<li class="page-item"><a class="page-link" rel="prev" href="{{url}}">{{text}}</a></li>',
		'prevDisabled' => '<li class="page-item disabled"><a class="page-link" href="" onclick="return false;">{{text}}</a></li>',
		'first' => '<li class="page-item"><a class="page-link" href="{{url}}">{{text}}</a></li>',
		'last' => '<li class="page-item"><a class="page-link" href="{{url}}">{{text}}</a></li>',
		'number' => '<li class="page-item"><a class="page-link" href="{{url}}">{{text}}</a></li>',
		'current' => '<li class="page-item active"><a class="page-link" href="">{{text}}</a></li>',
	]);
?>
<nav class="mt-4 pt-2" aria-label="<?= __d('database_log', 'Page navigation') ?>">
	<ul class="pagination justify-content-center mb-2">
		<?= $this->Paginator->first('<i class="fas fa-angle-double-left"></i>', ['escape' => false]) ?>
		<?= $this->Paginator->prev('<i class="fas fa-angle-left"></i>', ['escape' => false]) ?>
		<?= $this->Paginator->numbers() ?>
		<?= $this->Paginator->next('<i class="fas fa-angle-right"></i>', ['escape' => false]) ?>
		<?= $this->Paginator->last('<i class="fas fa-angle-double-right"></i>', ['escape' => false]) ?>
	</ul>
	<p class="text-center text-muted small">
		<?= $this->Paginator->counter(__d('database_log', 'Page {{page}} of {{pages}}, showing {{current}} record(s) out of {{count}} total')) ?>
	</p>
</nav>
<?php
}
