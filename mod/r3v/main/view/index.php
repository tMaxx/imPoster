<div id="content">
<? !$view_child ?: $view_child->go() ?>
</div>
<? if ($rpane): ?>
	<div class="rpanel">
		<?= $user_data['login'] ?>
	</div>
<? endif ?>
