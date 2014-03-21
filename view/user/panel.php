<? $this->guard_nonrequest();
$options = array('/user' => r3v\User::id() ? 'Panel' : NULL);
if (r3v\User::id()) {
	$options['/user/logout'] = '-Wyloguj-';
	echo 'Witaj, '.r3v\User::$me->get('login').' . ';
} else {
	$options['/user/register'] = '&Rejestracja&';
	$options['/user/login'] = '+Zaloguj+';
}
foreach ($options as $k => $v)
	if ($v)
		echo sprintf(' <a href="%s">%s</a>', $k, $v);
