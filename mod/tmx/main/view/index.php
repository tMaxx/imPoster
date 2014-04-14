<div class="clear"></div>
<div id="menu">
<? foreach ((tmx\Menu::getLeft() + tmx\Menu::getRight()) as $k => $v): ?>
	<a href="<?= $k ?>"<?= $k == $subpage ? ' id="sel"' : '' ?>
		<?= isset($v['class']) ? 'class="'.implode(' ', $v['class']).'"' : '' ?>><?= $v['name'] ?></a>
<? endforeach; ?>
</div>
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
