<? $this->guard_nonrequest();
/*color:[type] title
smallest:options
normal:content*/
if (!isset($item)) {
	if ($item = (int) CMS\Vars::uri('task')) {
		$item = DB('Elem')->select()->where(array('elem_id' => $item))->obj();
		Elem::auth($item);
	} else
		throw new Error400();
}

$isList = $item->isList();

if (!isset($truncate))
	$truncate = false;
if (!isset($view))
	$view = false;
?>
<div class="elem-item <?= $isList ? 'list' : 'task' ?>">
	<h4>
		<? if ($view): ?>
			<?= $item->getName() ?>
		<? else: ?>
			<span class="type"><?= $isList ? '&equiv;' : '&bull;' ?></span>
			<a href="<?= $item->getViewLink(); ?>"><?= $item->getName() ?></a>
		<? endif ?>
	</h4>
	<div class="elem-options">
		<a href="<?= $item->getEditLink() ?>">Edytuj</a>
		<? if ($isList): ?>
			<a href="<?= $item->getAddNewItemToListLink() ?>">Dodaj wpis</a>
		<? endif ?>
	</div>
	<div class="clear"></div>
	<p><?= $truncate ? CMS\Sys::truncate($item->getContent(), 140, TRUE) : preg_replace("/(\r\n){2}/", '</p><p>', $item->getContent()) ?></p>
</div>
