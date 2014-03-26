<?
$form = new Form(array(
	'name' => 'taskedit',
	'fields' => array(
		'name' => array(
			'string',
			'label' => 'Tytuł',
			'attributes' => array('class' => 'big wide'),
		),
		'content' => array(
			'textarea',
			'label' => 'Treść',
			'attributes' => array('class' => 'big wide'),
		),
		'submit' => array(
			'submit',
			'value' => 'Wyślij'
		),
	),
));

$list_id = CMS\Vars::get(['list' => FALSE])['list'];
$isList = ($list_id !== FALSE);

if ($task) {
	$el = DB('Elem')->select()->where(array('elem_id' => (int) $task))->obj();

	if (!$el)
		throw new Error404();
	if ($el->getUserId() != $user_id)
		throw new Error403();
	if ($isList && !$el->isList())
		throw new Error400('Wpis nie jest listą!');

	if ($form->submitted) {
		$el->set($form->get());
		DB($el)->save();
		throw new Redirect('/task/my');
	} else {
		$form->set($el->toArray());
		if ($el->isList())
			$isList = TRUE;
	}
} elseif ($form->submitted) {
	$el = new Elem($form->get());
	$el->setUserId($user_id)->setTs(NOW);
	if ($eln = is_numeric($list_id))
		$el->setListId((int)$list_id);
	$db = DB($el)->insert();

	if ($isList && !$eln) {
		$el->setListId($el->getID());
		$db->save();
	}
	throw new Redirect('/task/my');
}

if ($isList) {
	if ($task)
		$header = 'Edycja listy';
	elseif ($list_id)
		$header = 'Nowy wpis listy';
	else
		$header = 'Nowa lista';
} else {
	if ($task)
		$header = 'Edycja wpisu';
	else
		$header = 'Nowy wpis';
}
?>
<h3><?= $header ?></h3>
<?
$form->r();
