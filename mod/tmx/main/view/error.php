<div class="clear"></div>
<div class="errorhttp center">
	<h1 class="white"><?= $is_http ? 'HTTP '.$error->httpcode : 'Error! &nbsp; '.get_class($error) ?></h1>
	<span class="emsg"><?= (method_exists($error, 'getExtMessage') ? $error->getExtMessage() : $error->getMessage()) ?></span>
	<? if (DEBUG): ?>
		<div class="trace">
			<div class="location"><i>=></i> <?= rev\Error::pathdiff($error->getFile()), ':', $error->getLine() ?></div>
			<?= rev\Error::prettyTrace($error->getTrace()) ?>
		</div>
	<? endif ?>
</div>
