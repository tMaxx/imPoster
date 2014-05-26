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
	'/blog' => 'blog',
	'/about' => 'about',
]);
if (rev\Auth\User::id()) {
	tmx\Menu::addright(['/user:logout' => 'out']);
	if (rev\Auth\User::role('admin'))
		tmx\Menu::addright(['/admin' => '<small>cmd</small>:'.rev\Auth\User::name()]);
	else
		tmx\Menu::addright(['/user' => '<small>usr</small>:'.rev\Auth\User::name()]);
} else
	tmx\Menu::addright(['/user:login' => 'in']);
tmx\Menu::addright(['/locker' => 'L']);

return [
	'view_content' => $_view_child,
	'subpage' => rev\Vars::uri('rev/path'),
	'user_data' => rev\Auth\User::$user,
];
