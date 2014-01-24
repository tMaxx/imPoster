<?
$mode = CMS\Vars::uri('my');
$items = DB('Elem')->select()->where(array('user_id' => $user_id));

switch ($mode) {
	case 'tasks':
		$items->where('list_id IS NULL');
		break;
	case 'lists':
		$items->where('elem_id = list_id AND list_id IS NOT NULL');
		break;
}

$items = $items->orderby('ts DESC, is_read DESC')->objs();
?>
<a href="/task/edit" class="button big wide">Dodaj wpis</a> <a href="/task/edit?list" class="button big wide">Dodaj listę</a>
<? if (!$items): ?>
<h2>Brak wpisów do wyświetlenia</h2>
<? endif;
foreach ($items as $el)
	$this->subnode('/task/item', array('item' => $el, 'truncate' => TRUE));
