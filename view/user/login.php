<?php
if (CMS\Me::id())
	throw new Redirect('/');

$form = new Form(array(
	'name' => 'loginform',
	'attributes' => array(
		'class' => 'center',
	),
	'fields' => array(
		'email' => array(
			'email',
			'label' => 'Email',
		),
		'password' => array(
			'password',
			'label' => 'Hasło',
		),
		'sub' => array(
			'submit',
			'value' => 'Zaloguj',
		),
	),
));

if ($form->submitted()) {
	$err = CMS\Me::login($form->get('email'), $form->get('password'));
	if ($err === false)
		echo 'Brak takiego użytkownika';
	elseif ($err === 0)
		echo 'Nieprawidłowa para email-hasło';
	elseif ($err)
		$this->redirect('/');
}

$form->r();
