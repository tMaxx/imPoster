<?
$this->guard_nonrequest();
$options = array('/user' => FALSE ? 'User panel' : NULL, '/user/login' => 'Log in');
foreach ($options as $k => $v)
	if ($v)
		echo sprintf('<a href="%s">%s</a>', $k, $v);
