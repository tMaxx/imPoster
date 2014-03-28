<?php ///r3vNgine static files server
r3v\Mod::loadMod('scssphp');
$vars = r3v\Vars::uri(array('scss', 'js'));
$servepath = '/mod/r3v/static/';

switch (true) {
	case isset($vars['scss']): {
		$_GET['p'] = r3v\File::sanitizePath($vars['scss']).'.scss';

		if (!r3v\File::fileExists($servepath.$_GET['p']))
			break;

		$this->setContentType('css');
		$compiler = new scssc();
		$compiler->setFormatter('scss_formatter_compressed');

		$server = new scss_server(ROOT.$servepath, ROOT.'/cache', $compiler);
		$server->serve();
		unset($_GET['p']);
		return;
	}

	case isset($vars['js']): {
		if (!($cnt = r3v\File::contents(
				$servepath . r3v\File::sanitizePath($vars['js']) . '.js'
			)))
			break;
		$this->setContentType('js');
		echo $cnt;
		return;
	}

	default:
		throw new Error400('Unsupported');
		break;
}

throw new Error404();
