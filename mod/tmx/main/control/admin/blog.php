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
	$cobj = $crud->obj;
	if ($v[1] == 'new')
		$cobj->create();
	elseif (!$cobj->exists($v[1]))
		return [404];

	if ($cobj->submitted) {
		$cobj->id = $v[1];
		$cobj->formToLocal()->save();
		$this->redirect('/admin/blog:entry:'.$cobj->id);
	} else
		$cobj->load($v[1]);

	return [
		'/admin/blog_entry',
		'form' => $cobj->form,
		'id' => $cobj->id
	];
}

return [404];
