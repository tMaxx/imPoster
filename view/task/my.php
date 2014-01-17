<?
$items = DB('Elem')->select()->where(array('user_id' => $user_id))->orderby('ts DESC')->objs();
?>
<a href="/task/edit" class="button big wide">Dodaj nowy</a>
<? if (!$items): ?>
<h2>Brak wpisów do wyświetlenia</h2>
<? endif;
foreach ($items as $el)
	$this->subnode('/task/item', array('item' => $el, 'truncate' => TRUE));
