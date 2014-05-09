<?
if (!class_exists('rev\Auth\User', false))
	throw new rev\Error('User auth not loaded!');

if ($_view_child) {
	ob_start();
	$_view_child->go();
	$_view_child = ob_get_clean();
} else
	$_view_child = '';

tmx\Menu::append([
	'/blog' => 'Blog',
	'/about' => 'About',
	// '/findme' => 'Find me'
]);
if (rev\Auth\User::id()) {
	tmx\Menu::addright(['/user:logout' => '&#x1f51a;']);//
	if (rev\Auth\User::role('admin'))
		tmx\Menu::addright(['/admin' => '<small>cmd:'.rev\Auth\User::name().'</small>']);
	else
		tmx\Menu::addright(['/user' => '<small>usr:'.rev\Auth\User::name().'</small>']);
} else
	tmx\Menu::addright(['/user:login' => '&#x21af;']);
tmx\Menu::addright(['/locker' => '&#x1f512;']);

return [
	'view_content' => $_view_child,
	'subpage' => rev\Vars::uri('rev/path'),
	'user_data' => rev\Auth\User::$user,
];
