<?php
/*$vars = CMS::vars('GET', array('login', 'password'));
if (User::login($vars)) {
	//hallelujah!
	//TODO
	new DB('INSERT INTO UserSessions VALUES (...)')->param($vars)->exec();
	//generalnie redirect, rozwiązanie z dupy
	throw new ErrorHTTP('Redirect', 300);
} else {
	//chyba śnisz, walnij jakimś błędem
}
*/

$form = new Form(
	array(
		'name' => 'loginform', 
		'fields' => array(
			'user' => array(
				'text',
				'label' => 'Username:',
			), 
			'pwd' => array(
				'password',
				'label' => 'Password:',
			), 
			'sub' => array(
				'submit',
				'value' => 'Wyslij',
			),
		),
	)
);

$form->r();
