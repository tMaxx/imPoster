<? $this->guard_nonrequest();
if (!isset($item)) {
	if ($item = (int) CMS\Vars::uri('task')) {
		$item = DB('Elem')->select()->where(array('elem_id' => $item))->obj();
		Elem::auth($item);
	} else
		throw new Error400();
}
if (!isset($truncate))
	$truncate = false;
if (!isset($view))
	$view = false;

$isList = $item->isList();

$name = $item->getName();
if ($item->getIsRead())
	$name = '&#10003; '.$name;

if ($username = ($item->getUserId() != CMS\Me::id()))
	$username = DB('SELECT login FROM User WHERE user_id = ?')->param('i', $item->getUserId())->val();
else
	$username = '';

if ($assigned = $item->getUserDest())
	$assigned = ': '.(DB('SELECT login FROM User WHERE user_id=?')->param('i', $assigned)->val());
else
	$assigned = '';
?>
<div class="elem-item <?= $isList ? 'list' : 'task' ?>">
	<h4<?= $item->getIsRead() ? ' class="finished"' : '' ?>>
		<? if ($view): ?>
			<?= $name ?>
		<? else: ?>
			<span class="type"><?= $isList ? '&equiv;' : '&bull;' ?></span>
			<a href="<?= $item->getViewLink(); ?>"><?= $name ?></a>
		<? endif ?>
	</h4>
	<div class="elem-options">
		<?= $username ?> <?= $assigned ?>
		<? if ($isList && !$item->getIsRead()): ?>
			<a href="<?= $item->getAddNewItemToListLink() ?>">Dodaj wpis</a>
		<? endif;
			if (!$item->getIsRead()): ?>
			<a href="/task:<?= $item->getId() ?>/actions:assign">Przypisz</a>
			<a href="/task:<?= $item->getId() ?>/actions:finished">Zrobione</a>
		<? endif ?>
		<a href="<?= $item->getEditLink() ?>">Edytuj</a>
	</div>
	<div class="clear"></div>
	<p><?= $truncate ? CMS\Sys::truncate($item->getContent(), 140, TRUE) : preg_replace("/(\r\n){2}/", '</p><p>', $item->getContent()) ?></p>
</div>
