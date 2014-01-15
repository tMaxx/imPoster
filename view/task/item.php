<? $this->guard_nonrequest() 
/*color:[type] title
smallest:options
normal:content*/
?>
<div class="elem-item task">
	<h4><span class="type id-<?= $item->getType() ?>"><?= $item->isList() ? '≡' : '&middot;' ?></span> <?= $item->getName() ?></h4>
	<div class="elem-options"><span class="type-small"><?= $item->getTypeString() ?></span> <a href="<?= $item->getEditLink() ?>">Edytuj</a></div>
	<p><?= $item->getContent() ?></p>
</div>
