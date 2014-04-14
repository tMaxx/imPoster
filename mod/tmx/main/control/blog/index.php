<?

if (is_numeric($id = r3v\Vars::uri('blog'))) {
	$single = DB('SELECT * FROM Blog WHERE id=?')->param('i', $id)->row();
	if (!$single)
		throw new r3v\Error404();
	return [
		'/blog/single',
		'single' => $single
	];
}


$entr = DB('SELECT * FROM Blog')->rows();


return [
	'entr' => $entr
];
