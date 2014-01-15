<?
$var = CMS\Vars::uri(array('scss' => NULL));
if (!$var['scss'])
	echo "/* Unknown var: $var[scss] */";
else {
	$_GET['p'] = CMS::sanitizePath($var['scss']).'.scss';
	$compiler = new scssc();
	$compiler->setFormatter('scss_formatter_compressed');

	$server = new scss_server(ROOT.'/appdata', 'appdata/cache/scss', $compiler);
	$server->serve();
	unset($_GET);
}
