<?php
throw new rev\Error501('FIX ME, PLEASE...!');
if ($reg = rev\Vars::get('confirm')) {
	$reg = explode(':', $reg, 2);
	$el = \rev\DB\Q('User')->select()->where(['user_id' => $reg[0], 'is_active' => false, 'is_removed' => false])->obj();
	if (!$el)
		throw new rev\Error404('Użytkownik nie istnieje lub konto zostało już aktywowane');
	$hash = substr(hash('sha256', $el->getId().$el->getEmail().$el->getLogin()), 0, 10);
	if ($hash == $reg[1]) {
		$el->setIsActive(true);
		\rev\DB\Q($el)->save();
		$this->redirect('/user/login');
	} else
		throw new rev\Error400('Zły ciąg aktywacyjny');
}

if (rev\Auth\User::id())
	$this->redirect('/');

if ($form->submitted()) {
	$data = $form->get();

			$tmp = new \rev\Template('register_mail', 'templates');
			$tmp->replace([

			]);
			\rev\Mail::create($uinfo->email, \rev\Conf::get('site/title').': rejestracja konta', $tmp->get());
			\rev\Mail::flush();


	if ($data['password'] != $data['repeat'])
		$form->error('Hasła nie są równe', 'repeat');
	else {
		$reg = rev\Me::register($data['email'], $data['login'], $data['password']);
		if ($reg === false)
			$form->error('Taka nazwa użytkownika lub email został już użyty', 'email');
		elseif (!$reg)
			echo 'Wystąpił błąd podczas rejestracji';
		else {
			$hash = $reg.':'.substr(hash('sha256', $reg.$data['email'].$data['login']), 0, 10);
			$hash = HOST.'/user/confirm:'.$hash;
			echo 'OK';

			$tmp = new rev\Template(__DIR__.'/register_mail.html', 'abs');
			$tmp->replace([
				'HOST' => HOST,
				'LOGIN' => $data['login'],
				'CONFIRM' => $hash
			]);

			rev\Mail::create($data['email'], '[theMaksiu] Rejestracja', $tmp->get());
			rev\Mail::flush();

			$this->redirect('/');
		}
	}
}

$form->r();

