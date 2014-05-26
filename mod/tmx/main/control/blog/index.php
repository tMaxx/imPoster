<?
$crud = new \rev\CRUD\CRUD('blog', 'user');

$id = ($id = rev\Vars::uri(['blog'])) ? $id['blog'] : false;
if ($id) {
	if (ctype_digit($id)) {
		if (!$crud->object()->load($id))
			return [404];

		$single = $crud->object()->values;
		$single['tags'] = \tmx\Blog::getTags($id);

		return [
			'/blog/single',
			'single' => $single
		];
	}
	return [404];
}
$this->sub('/blog/page', ['crud' => $crud]);
