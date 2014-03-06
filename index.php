<?php ///r3vCMS /index.php
//set charset
ini_set('default_charset', 'UTF-8');
mb_internal_encoding('UTF-8');
mb_regex_encoding('UTF-8');

//CMS version
define('r3v_VERSION', '0.6alpha1');
//CMS identificator
define('r3v_ID', 'revCMS [elementary] v'.r3v_VERSION);

//time
define('NOW_MICRO', /*(int)*/(microtime(true) * 10000));
define('NOW', time());

//location
define('ROOT', __DIR__);

//properties
define('AJAX', (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'));
if (PHP_SAPI == 'cli') {
	define('CLI', TRUE);
	define('HOST', 'interactive');
	cli_set_process_title('r3vCMS');
} else {
	define('CLI', FALSE);
	define('HOST', $_SERVER['HTTP_HOST']);
}
if (!defined('DEBUG')) {
	if (strtolower(getenv('r3vDEBUG')) == 'true')
		define('DEBUG', TRUE);
	else
		define('DEBUG', FALSE);
}

//redirect to init file
require_once ROOT.'/sys/_init.php';

CMS\Mod::entrypoint();
