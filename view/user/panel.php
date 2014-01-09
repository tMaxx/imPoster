<?
$this->guard_nonrequest();
$options = array('/user' => CMS\Me::id() ? 'Panel' : NULL);
if (CMS\Me::id()) {
	$options['/user/logout'] = '-Wyloguj-';
	echo 'Witaj, '.CMS\Me::$me->get('login').' . ';
} else
	$options['/user/login'] = '+Zaloguj+';
foreach ($options as $k => $v)
	if ($v)
		echo sprintf(' <a href="%s">%s</a>', $k, $v);
