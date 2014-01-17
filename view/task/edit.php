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

if ($task) {
	$el = DB('Elem')->select()->where(array('elem_id' => (int) $task))->obj();

	if (!$el)
		throw new Error404();
	if ($el->getUserId() != $user_id)
		throw new Error403();

	if ($form->submitted) {
		$el->set($form->get());
		DB($el)->save();	
		throw new Redirect('/task/my');
	} else
		$form->set($el->toArray());
} elseif ($form->submitted) {
	$el = new Elem($form->get());
	$el->setUserId($user_id)->setTs(NOW);
	DB($el)->insert();	
	throw new Redirect('/task/my');
}

if ($task): ?>
<h3>Edycja wpisu</h3>
<? else: ?>
<h3>Nowy wpis</h3>
<? endif;

$form->r();
