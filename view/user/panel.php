<?
$this->guard_nonrequest();
foreach (array('/' => 'Main', '/user' => 'User panel', '/user/login' => 'Log in') as $k => $v)
	echo sprintf('<a href="%s">%s</a>', $k, $v);
