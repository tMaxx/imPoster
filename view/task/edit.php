<?
$form = new Form(array(
	'name' => 'taskedit',
	'fields' => array(
		'name' => array(
			'string',
			'label' => 'Tytuł',
			'attributes' => array('class' => 'big'),
		),
		'content' => array(
			'textarea',
			'label' => 'Treść',
			'attributes' => array('class' => 'big'),
		),
		'submit' => array(
			'submit',
			'value' => 'Wyślij'
		),
	),
));

if ($form->submitted()) {
	$en = DB($el = new Elem($form->get()));
	$el->setUserId($user_id)->setTs(NOW);
	$en->insert();
	throw new Redirect('/task/my');
} elseif ($task) {

	//TODO: get task from db and display it here
}


$form->r();
