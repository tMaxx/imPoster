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

if (!isset($truncate))
	$truncate = false;
?>
<div class="elem-item task">
	<h4><span class="type id-<?= $item->getType() ?>"><?= $item->isList() ? '≡' : '&middot;' ?></span> <a href="<?= $item->getViewLink(); ?>"><?= $item->getName() ?></a></h4>
	<div class="elem-options"><span class="type-small"><?= $item->getTypeString() ?></span> <a href="<?= $item->getEditLink() ?>">Edytuj</a></div>
	<p><?= $truncate ? CMS\Sys::truncate($item->getContent()) : $item->getContent() ?></p>
</div>
