<div class="clear"></div>
<div class="errorhttp center">
<h1 class="white">HTTP <?= $error->httpcode ?></h1>
<span class="emsg"><?= $error->inmessage ?></span>
<? if (DEBUG): ?>
	<div class="trace">
		<div class="location"><i>-></i> <?= Error::pathdiff($error->getFile()), ':', $error->getLine() ?></div>
		<?= Error::prettyTrace($error->getTrace()) ?>
	</div>
<? endif ?>
</div>
