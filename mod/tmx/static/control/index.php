<?php ///tmx static files server
$vars = rev\Vars::uri(array('scss', 'js'));
$servepath = rev\View::getCurrentBasepath().'res/';
$force = !!rev\Vars::get(['force']);

switch (true) {
	case isset($vars['scss']): {
		$_GET['p'] = rev\File::sanitizePath($vars['scss']).'.scss';

		if (!rev\File::fileExists($servepath.$_GET['p']))
			break;

		if ($force)
			clearstatcache();
		elseif ($vars['scss'][0] == '_')
			break;

		if (rev\View::setCacheControl($servepath.$_GET['p'], $force))
			return;

		$this->setContentType('css');

		rev\Mod::loadMod('lib/scssphp');

		$compiler = new scssc();
		$compiler->setFormatter('scss_formatter_compressed');
		$compiler->addImportPath(ROOT.$servepath);

		$server = new scss_server(ROOT.$servepath, ROOT.'/cache', $compiler);
		$server->serve();
		unset($_GET['p']);
		return;
	}

	case isset($vars['js']): {
		$servepath .= rev\File::sanitizePath($vars['js']) . '.js';
		if (!rev\File::fileExists($servepath))
			break;
		if (rev\View::setCacheControl($servepath))
			return;

		$this->setContentType('js');
		echo rev\File::contents($servepath);
		return;
	}

	default:
		throw new Error400('Unsupported');
		break;
}

return [404];
