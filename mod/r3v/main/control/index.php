<?
//init user data
r3v\Auth\User::load();
// vdump(r3v\Auth\Session::id(), r3v\Auth\Session::dump());


return [
	'view_child' => $__view_child,
	'rpane' => !!r3v\Auth\User::id(),
	'user_data' => r3v\Auth\User::$user,
];
