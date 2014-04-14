<?
if (!class_exists('r3v\Auth\User', false))
	throw new r3v\Error('User auth not loaded!');

tmx\Menu::append([
	'/blog' => 'Blog',
	'/about' => 'About'
]);
if (r3v\Auth\User::id()) {
	tmx\Menu::addright(['/user:logout' => '&#x1f51a;']);
	if (r3v\Auth\User::role('admin'))
		tmx\Menu::addright(['/tmx/admin' => '&#x2318;']);
} else
	tmx\Menu::addright(['/user:login' => '&#x21af;']);
tmx\Menu::addright(['/locker' => '&#x1f512;']);

if ($_view_child) {
	ob_start();
	$_view_child->go();
	$_view_child = ob_get_clean();
} else
	$_view_child = '';

return [
	'view_content' => $_view_child,
	'subpage' => r3v\Vars::uri('r3v/path'),
	'rpane' => !!r3v\Auth\User::id(),
	'user_data' => r3v\Auth\User::$user,
];
