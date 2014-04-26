<?php ///tmx static files server
$vars = r3v\Vars::uri(array('scss', 'js'));
$servepath = r3v\View::getCurrentBasepath().'res/';

function setCacheControl($path) {
	$path = ROOT.$path;
	$mtime = filemtime($path);
	$time = gmdate('r', $mtime);
	$etag = md5($mtime.$path);

	$exptime = $time;
	while (($exptime += 2764800) <= (NOW+2764800));

	r3v\View::addHTTPheaders([
		"Last-Modified: $time",
		// "Cache-Control: must-revalidate",
		'Expires: '.gmdate('r', $exptime),
		"Etag: $etag",
	]);

	if ((isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])
		&& $_SERVER['HTTP_IF_MODIFIED_SINCE'] == $time) ||
		(isset($_SERVER['HTTP_IF_NONE_MATCH'])
		&& str_replace('"', '', stripslashes($_SERVER['HTTP_IF_NONE_MATCH'])) == $etag)) {
		r3v\View::addHTTPheaders([
			'HTTP/1.1 304 Not Modified',
			'Content-Length: 0',
		]);
		r3v\View::flushHTTPheaders();
		return true;
	}
	return false;
}

switch (true) {
	case isset($vars['scss']): {
		$_GET['p'] = r3v\File::sanitizePath($vars['scss']).'.scss';

		if (!r3v\File::fileExists($servepath.$_GET['p']))
			break;
		if ($vars['scss'][0] == '_')
			break;

		if (setCacheControl($servepath.$_GET['p']))
			return;

		$this->setContentType('css');

		r3v\Mod::loadMod('lib/scssphp');

		$compiler = new scssc();
		$compiler->setFormatter('scss_formatter_compressed');
		$compiler->addImportPath(ROOT.$servepath);

		$server = new scss_server(ROOT.$servepath, ROOT.'/cache', $compiler);
		$server->serve();
		unset($_GET['p']);
		return;
	}

	case isset($vars['js']): {
		$servepath .= r3v\File::sanitizePath($vars['js']) . '.js';
		if (!r3v\File::fileExists($servepath))
			break;
		if (setCacheControl($servepath))
			return;

		$this->setContentType('js');
		echo r3v\File::contents($servepath);
		return;
	}

	default:
		throw new Error400('Unsupported');
		break;
}

throw new Error404();
