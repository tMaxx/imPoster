<? $this->guard_nonrequest();
$items = array('/' => 'About');
$cur = CMS\Vars::uri('r3v/path');
if (!$cur)
	$cur = '/';
if (CMS\Me::id())
	$items['/task/my'] = 'Moje zadania';
?>
<div id="menu">
<? foreach ($items as $k => $v): ?>
	<a href="<?= $k ?>"<?= $k == $cur ? ' id="cur"' : '' ?>><?= $v ?></a>
<? endforeach; ?>
</div>
<div class="clear"></div>
