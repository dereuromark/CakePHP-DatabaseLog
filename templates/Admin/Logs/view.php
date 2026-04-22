<?php
/**
 * @var \App\View\AppView $this
 * @var \DatabaseLog\Model\Entity\DatabaseLog $log
 */

$isCli = $log->isCli();
$formatted = $this->request->getQuery('formatted');
?>

<div class="d-flex justify-content-between align-items-center mb-4">
	<h1 class="mb-0">
		<i class="fas fa-file-alt me-2 text-muted"></i>
		<?= __d('database_log', 'Log') ?> #<?= h($log->id) ?>
	</h1>
	<div>
		<button type="button" class="btn btn-outline-secondary btn-sm" data-action="copy-full-log" title="<?= __d('database_log', 'Copy full log') ?>">
			<i class="fas fa-copy me-1"></i>
			<?= __d('database_log', 'Copy All') ?>
		</button>
		<?php if ($formatted): ?>
		<a href="<?= $this->Url->build([$log->id, '?' => array_diff_key($this->request->getQuery(), ['formatted' => true])]) ?>" class="btn btn-outline-secondary btn-sm">
			<i class="fas fa-align-left me-1"></i>
			<?= __d('database_log', 'Normal') ?>
		</a>
		<?php else: ?>
		<a href="<?= $this->Url->build([$log->id, '?' => ['formatted' => true] + $this->request->getQuery()]) ?>" class="btn btn-outline-secondary btn-sm">
			<i class="fas fa-code me-1"></i>
			<?= __d('database_log', 'Formatted') ?>
		</a>
		<?php endif; ?>
		<a href="<?= $this->Url->build(['action' => 'index', '?' => $this->request->getQuery()]) ?>" class="btn btn-outline-primary btn-sm">
			<i class="fas fa-arrow-left me-1"></i>
			<?= __d('database_log', 'Back') ?>
		</a>
		<?= $this->Form->postButton(
			'<i class="fas fa-trash me-1"></i>' . __d('database_log', 'Delete'),
			['action' => 'delete', $log->id],
			[
				'class' => 'btn btn-outline-danger btn-sm',
				'escapeTitle' => false,
				'form' => [
					'class' => 'd-inline',
					'data-confirm-message' => __d('database_log', 'Delete this log entry?'),
				],
			]
		) ?>
	</div>
</div>

<div class="card card-dblog mb-4">
	<div class="card-header">
		<i class="fas fa-info-circle me-2"></i>
		<?= __d('database_log', 'Details') ?>
	</div>
	<div class="card-body">
		<dl class="row mb-0">
			<dt class="col-sm-3"><?= __d('database_log', 'Type') ?></dt>
			<dd class="col-sm-9">
				<?= $this->Log->typeLabel($log->type) ?>
				<?php if ($isCli): ?>
				<span class="badge bg-secondary ms-1">cli</span>
				<?php endif; ?>
			</dd>

			<dt class="col-sm-3"><?= __d('database_log', 'Created') ?></dt>
			<dd class="col-sm-9"><?= $this->Time->nice($log->created) ?></dd>

			<dt class="col-sm-3"><?= $isCli ? __d('database_log', 'Command') : __d('database_log', 'URI') ?></dt>
			<dd class="col-sm-9"><?= h($log->uri) ?: '-' ?></dd>

			<?php if (!$isCli): ?>
			<dt class="col-sm-3"><?= __d('database_log', 'Referrer') ?></dt>
			<dd class="col-sm-9"><?= h($log->refer) ?: '-' ?></dd>
			<?php endif; ?>

			<dt class="col-sm-3"><?= __d('database_log', 'Hostname') ?></dt>
			<dd class="col-sm-9"><?= h($log->hostname) ?: '-' ?></dd>

			<dt class="col-sm-3"><?= __d('database_log', 'IP') ?></dt>
			<dd class="col-sm-9"><?= h($log->ip) ?: '-' ?></dd>

			<dt class="col-sm-3"><?= __d('database_log', 'User Agent') ?></dt>
			<dd class="col-sm-9"><small><?= h($log->user_agent) ?: '-' ?></small></dd>
		</dl>
	</div>
</div>

<?php if ($log->summary && !$log->message): ?>
<div class="card card-dblog mb-4">
	<div class="card-header d-flex justify-content-between align-items-center">
		<span>
			<i class="fas fa-align-left me-2"></i>
			<?= __d('database_log', 'Summary') ?>
		</span>
		<button type="button" class="btn btn-outline-secondary btn-sm copy-btn" data-target="summary-content" title="<?= __d('database_log', 'Copy') ?>">
			<i class="fas fa-copy"></i>
		</button>
	</div>
	<div class="card-body">
		<?php if ($formatted): ?>
		<pre class="mb-0" id="summary-content"><?= trim(h($log->summary)) ?></pre>
		<?php else: ?>
		<div id="summary-content"><?= trim(nl2br(h($log->summary))) ?></div>
		<?php endif; ?>
	</div>
</div>
<?php endif; ?>

<div class="card card-dblog mb-4">
	<div class="card-header d-flex justify-content-between align-items-center">
		<span>
			<i class="fas fa-envelope me-2"></i>
			<?= __d('database_log', 'Message') ?>
		</span>
		<button type="button" class="btn btn-outline-secondary btn-sm copy-btn" data-target="message-content" title="<?= __d('database_log', 'Copy') ?>">
			<i class="fas fa-copy"></i>
		</button>
	</div>
	<div class="card-body">
		<?php if ($formatted): ?>
		<pre class="mb-0" id="message-content"><?= trim(h($log->message)) ?></pre>
		<?php else: ?>
		<div id="message-content"><?= trim(nl2br(h($log->message))) ?></div>
		<?php endif; ?>
	</div>
</div>

<?php if ($log->context): ?>
<div class="card card-dblog">
	<div class="card-header d-flex justify-content-between align-items-center">
		<span>
			<i class="fas fa-code me-2"></i>
			<?= __d('database_log', 'Context') ?>
		</span>
		<button type="button" class="btn btn-outline-secondary btn-sm copy-btn" data-target="context-content" title="<?= __d('database_log', 'Copy') ?>">
			<i class="fas fa-copy"></i>
		</button>
	</div>
	<div class="card-body">
		<?php if ($formatted): ?>
		<pre class="mb-0" id="context-content"><?= trim(h($log->context)) ?></pre>
		<?php else: ?>
		<div id="context-content"><?= trim(nl2br(h($log->context))) ?></div>
		<?php endif; ?>
	</div>
</div>
<?php endif; ?>

<?php
$this->append('script');
$cspNonce = (string)$this->getRequest()->getAttribute('cspNonce', '');
?>
<script<?= $cspNonce !== '' ? ' nonce="' . h($cspNonce) . '"' : '' ?>>
// Copy individual section
document.querySelectorAll('.copy-btn').forEach(function(btn) {
	btn.addEventListener('click', function() {
		var targetId = this.getAttribute('data-target');
		var content = document.getElementById(targetId);
		if (content) {
			copyToClipboard(content.innerText, this);
		}
	});
});

// Copy full log (triggered by button[data-action="copy-full-log"])
function copyFullLog(btn) {
	var fullLog = <?= json_encode([
		'id' => $log->id,
		'type' => $log->type,
		'created' => $log->created ? $log->created->format('Y-m-d H:i:s') : null,
		'uri' => $log->uri,
		'hostname' => $log->hostname,
		'ip' => $log->ip,
		'message' => $log->message,
		'context' => $log->context,
	]) ?>;

	var text = "Log #" + fullLog.id + "\n";
	text += "Type: " + fullLog.type + "\n";
	text += "Created: " + fullLog.created + "\n";
	text += "URI: " + (fullLog.uri || '-') + "\n";
	text += "Hostname: " + (fullLog.hostname || '-') + "\n";
	text += "IP: " + (fullLog.ip || '-') + "\n";
	text += "\n--- Message ---\n" + (fullLog.message || '-') + "\n";
	if (fullLog.context) {
		text += "\n--- Context ---\n" + fullLog.context + "\n";
	}

	copyToClipboard(text, btn);
}

document.querySelectorAll('[data-action="copy-full-log"]').forEach(function(btn) {
	btn.addEventListener('click', function() {
		copyFullLog(this);
	});
});

function copyToClipboard(text, btn) {
	navigator.clipboard.writeText(text).then(function() {
		var originalHtml = btn.innerHTML;
		btn.innerHTML = '<i class="fas fa-check"></i>';
		btn.classList.remove('btn-outline-secondary');
		btn.classList.add('btn-success');
		setTimeout(function() {
			btn.innerHTML = originalHtml;
			btn.classList.remove('btn-success');
			btn.classList.add('btn-outline-secondary');
		}, 1500);
	});
}
</script>
<?php $this->end(); ?>
