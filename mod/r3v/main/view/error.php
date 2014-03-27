<div class="clear"></div>
<div class="errorhttp center">
<h1 class="white">HTTP <?= $error->httpcode ?></h1>
<?= $error->inmessage ?>
<? if (DEBUG): ?>
	<div class="trace">
		<div class="location"><i>@</i><?= Error::pathdiff($error->getFile()), ':', $error->getLine() ?></div>
		<?= Error::prettyTrace($error->getTrace()) ?>
	</div>
<? endif ?>
</div>
