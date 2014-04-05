<?php
throw new r3v\Error501('FIX ME, PLEASE...!');
if ($reg = r3v\Vars::get('confirm')) {
	$reg = explode(':', $reg, 2);
	$el = DB('User')->select()->where(['user_id' => $reg[0], 'is_active' => false, 'is_removed' => false])->obj();
	if (!$el)
		throw new r3v\Error404('Użytkownik nie istnieje lub konto zostało już aktywowane');
	$hash = substr(hash('sha256', $el->getId().$el->getEmail().$el->getLogin()), 0, 10);
	if ($hash == $reg[1]) {
		$el->setIsActive(true);
		DB($el)->save();
		$this->redirect('/user/login');
	} else
		throw new r3v\Error400('Zły ciąg aktywacyjny');
}

if (r3v\Auth\User::id())
	$this->redirect('/');

if ($form->submitted()) {
	$data = $form->get();

			$tmp = new \r3v\Template('register_mail', 'templates');
			$tmp->replace([

			]);
			\r3v\Mail::create($uinfo->email, \r3v\Conf::get('site/title').': rejestracja konta', $tmp->get());
			\r3v\Mail::flush();


	if ($data['password'] != $data['repeat'])
		$form->error('Hasła nie są równe', 'repeat');
	else {
		$reg = r3v\Me::register($data['email'], $data['login'], $data['password']);
		if ($reg === false)
			$form->error('Taka nazwa użytkownika lub email został już użyty', 'email');
		elseif (!$reg)
			echo 'Wystąpił błąd podczas rejestracji';
		else {
			$hash = $reg.':'.substr(hash('sha256', $reg.$data['email'].$data['login']), 0, 10);
			$hash = HOST.'/user/confirm:'.$hash;
			echo 'OK';

			$tmp = new r3v\Template(__DIR__.'/register_mail.html', 'abs');
			$tmp->replace([
				'HOST' => HOST,
				'LOGIN' => $data['login'],
				'CONFIRM' => $hash
			]);

			r3v\Mail::create($data['email'], '[theMaksiu] Rejestracja', $tmp->get());
			r3v\Mail::flush();

			$this->redirect('/');
		}
	}
}

$form->r();

