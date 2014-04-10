<div id="content">
<? if ($view_content): ?>
	<?= $view_content ?>
<? else: ?>
	<h3>Hi :D</h3>
<? endif; ?>
</div>
<? if ($rpane): ?>
	<div class="rpanel">
		<?= $user_data['login'] ?>
	</div>
<? endif ?>
