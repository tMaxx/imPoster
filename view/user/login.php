<?php
$form = new Form(
	array(
		'name' => 'loginform', 
		'attributes' => array(
			'class' => 'center',
		),
		'fields' => array(
			'user' => array(
				'email',
				'label' => 'Email:',
			), 
			'pwd' => array(
				'password',
				'label' => 'Hasło:',
			), 
			'sub' => array(
				'submit',
				'value' => 'Wyślij',
			),
		),
	)
);

$form->r();

if ($form->submitted())
	echo 'submitted';
/*
if (User::login($vars)) {
	//hallelujah!
	//TODO

	//generalnie redirect, rozwiązanie z dupy
	throw new ErrorHTTP('Redirect', 300);
} else {
	//chyba śnisz, walnij jakimś błędem
}
*/
