<?
\rev\Auth\User::roleThrow('admin');

$crud = new \rev\CRUD\CRUD('blog', 'admin');

if ($v = \rev\Vars::uri('blog'))
	$v = explode(':', $v, 2);
else
	$v = ['page', null];


if ($v[0] == 'page') {
	return [
		'/admin/blog_page',
		'entr' => $crud->page($v[1]),
		'pg' => $crud->navigation($v[1]),
	];
} elseif ($v[0] == 'entry') {
	$crud->object(); //launch
	if ($v[1] == 'new')
		$crud->object()->create();
	elseif (!$crud->object()->load($v[1]))
		return [404];

	return [
		'/admin/blog_entry',
		'form' => $crud->object()->form(),
		'id' => $crud->object()->id
	];
}

return [404];
