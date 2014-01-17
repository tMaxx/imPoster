<?
if (!$task)
	throw new Error404();

$item = DB('Elem')->select()->where(array('elem_id' => $task))->obj();
Elem::auth($item);

$this->subnode('/task/item', array('item' => $item));

foreach ($item->getListElements() as $o)
	$this->subnode('/task/item', array('item' => $o));
