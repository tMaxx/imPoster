<?php
if ($reg = CMS\Vars::get('confirm')) {
	$reg = explode(':', $reg, 2);
	$el = DB('User')->select()->where(['user_id' => $reg[0], 'is_active' => false, 'is_removed' => false])->obj();
	if (!$el)
		throw new Error404('Użytkownik nie istnieje lub konto zostało już aktywowane');
	$hash = substr(hash('sha256', $el->getId().$el->getEmail().$el->getLogin()), 0, 10);
	if ($hash == $reg[1]) {
		$el->setIsActive(true);
		DB($el)->save();
		$this->redirect('/user/login');
	} else
		throw new Error400('Zły ciąg aktywacyjny');
}

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
			$hash = $reg.':'.substr(hash('sha256', $reg.$data['email'].$data['login']), 0, 10);
			$hash = 'http://'.HOST.'/user/register?confirm='.$hash;
			echo 'OK';
			$body = "<!DOCTYPE html>
			<html lang=\"pl\">
			<head><meta charset=\"UTF-8\" /></head><body>
			Witaj!<br><br>Zarejestrowałeś/aś się w serwisie ".HOST." jako {$data['login']}.<br>
			Aby się zalogować potwierdź aktywację swojego konta kierując się pod link:<br>
			<a href=\"{$hash}\">{$hash}</a><br>
			Po aktywacji możesz się zalogować używając hasła podanego przy rejestracji.<br><br>~Zespół teo
			</body></html>";
			CMS\Mail::create($data['email'], '[theOrganizer] Rejestracja', $body);
			CMS\Mail::flush();
			$this->redirect('/');
		}
	} else
		$form->error('Hasła nie są równe', 'repeat');
}

$form->r();

