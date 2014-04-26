<?

if (isset($header))
	return [
		'/blog/header',
		'header' => $header
	];


if (is_numeric($id = rev\Vars::uri('blog'))) {
	$single = DB('SELECT * FROM Blog WHERE id=? AND is_draft=0')->param('i', $id)->row();
	if (!$single)
		throw new rev\Error404();

	$single['tags'] = DB('SELECT name FROM Tags WHERE blog_id=?')->param('i', $id)->vals();

	return [
		'/blog/single',
		'single' => $single
	];
}


$entr = DB('SELECT id, name, ts_publ FROM Blog')->rows();

return [
	'entr' => $entr
];
