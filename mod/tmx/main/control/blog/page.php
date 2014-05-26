<?
//may get cross-loaded from /blog/index.php
if (empty($crud))
	$crud = new \rev\CRUD\CRUD('blog', 'user');

$page = \rev\Vars::get('page');

$entr = $crud->page($page);
$overflow = count($entr);
if ($overflow > $crud->getIOPC())
	$overflow -= $crud->getIOPC();
else
	$overflow = null;

return [
	'/blog/list',
	'entr' => $entr,
	'overflow' => $overflow,
	'pg' => $crud->navigation($page)
];
