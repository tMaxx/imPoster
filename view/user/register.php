<?php

$form = new Form(array(
	'name' => 'formregister',
	'fields' => array(
		'email' => array('email',
			'label' => 'Email',
		),
		'login' => array('string',
			'label' => 'Nazwa użytkownika',
		),
		'password' => array('password',
			'label' => 'Hasło',
		),
		'repeat' => array('password',
			'label' => 'Powtórz hasło'
		),
		'submit' => array('submit',
			'label' => 'Zarejestruj',
		),
	),
));

if ($form->submitted()) {
	$data = $form->get();

	if ($data['password'] == $data['repeat']) {
		$reg = CMS\Me::register($data['email'], $data['login'], $data['password']);
		if ($reg === false)
			echo 'Taka nazwa użytkownika lub email został już użyty';
		elseif (!$reg)
			echo 'Wystąpił błąd podczas rejestracji';
		else {
			echo 'OK';
			$this->redirect('/');
		}
	} else
		echo 'Hasła nie są równe';
}

$form->r();

