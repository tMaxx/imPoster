<?php
if (CMS\Me::id())
	$this->redirect('/');

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
			$form->error('Taka nazwa użytkownika lub email został już użyty', 'email');
		elseif (!$reg)
			echo 'Wystąpił błąd podczas rejestracji';
		else {
			echo 'OK';
			$body = "<!DOCTYPE html>
			<html lang=\"pl\">
			<head><meta charset=\"UTF-8\" /></head><body>
			Witaj!<br>Zarejestrowałeś się w serwisie ".HOST." jako {$data['login']}.<br>
			Możesz już się zalogować używając hasła podanego przy rejestracji.<br><br>~Zespół teo
			</body></html>";
			CMS\Mail::create($data['email'], '[theOrganizer] Rejestracja', $body);
			CMS\Mail::flush();
			$this->redirect('/');
		}
	} else
		$form->error('Hasła nie są równe', 'repeat');
}

$form->r();

