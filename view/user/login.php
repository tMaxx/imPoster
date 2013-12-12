<?php
$form = new Form(array(
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
));


if ($form->submitted())
	echo '<h2>Form submitted</h2>';
else
	$form->r();
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
