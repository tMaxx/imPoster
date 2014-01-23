<?
if (!$task)
	throw new Error404();

$item = DB('Elem')->select()->where(array('elem_id' => $task))->obj();
Elem::auth($item);

$this->subnode('/task/item', array('item' => $item));

if ($list_items = $item->getListElements())
	foreach ($list_items as $o)
		$this->subnode('/task/item', array('item' => $o, 'view' => TRUE));
