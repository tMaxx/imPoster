<?php
// use r3v\Auth;
//let the shitstorm commence
vdump(r3v\Auth\GAuth::load());


if (isset($_GET['logout'])) { // logout: destroy token
	r3v\Auth\GAuth::logout();
}

if (r3v\Vars::uri('google') == 'callback') { // we received the positive auth callback, get the token and store it in session
	r3v\Auth\GAuth::auth($_GET['code']);

	$this->redirect('/user/google:dump');
}

if (r3v\Vars::uri('google') == 'auth')
	r3v\Auth\GAuth::login_redirect();

if (r3v\Vars::uri('google') == 'destroy')
	r3v\Auth\GAuth::logout();

if (r3v\Vars::uri('google') == 'dump')
	vdump(
		r3v\Auth\Session::dump(),
		r3v\Auth\GAuth::dump()
	);


echo 'Hello, world.';
