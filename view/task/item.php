<? $this->guard_nonrequest() 
/*color:[type] title
smallest:options
normal:content*/
?>
<h4><span class="type-<?= $item->getType() ?>">[]</span> <?= $item->getName() ?></h4>
<div class="elem-options"><span class="type-small"><?= $item->getTypeString() ?></span> <a href="<?= $item->getEditLink() ?>">Edytuj</a></div>
<p><?= $item->getContent() ?></p>
