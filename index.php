<?php ///r3vCMS /index.php
//set charset
ini_set('default_charset', 'UTF-8');
mb_internal_encoding('UTF-8');
mb_regex_encoding('UTF-8');

//CMS version
define('r3v_VERSION', '0.6alpha3');
//CMS identificator
define('r3v_ID', 'revCMS [elementary] v'.r3v_VERSION);

//time
define('NOW_MICRO', floor(microtime(true) * 10000));
define('NOW', time());

//location
define('ROOT', __DIR__);

//properties
define('AJAX', (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'));
define('CLI', (PHP_SAPI == 'cli'));
define('HOST', (CLI ? 'interactive' : $_SERVER['HTTP_HOST']));
define('REQUEST_URI', (CLI ? '/' : $_SERVER['REQUEST_URI']));
define('PROCESS_ID', posix_getpid());
if (!defined('DEBUG')) {
	if (strtolower(getenv('r3vDEBUG')) == 'true')
		define('DEBUG', TRUE);
	else
		define('DEBUG', FALSE);
}

//constant-dependent settings
if (DEBUG) {
	error_reporting(E_ALL);
	ini_set('log_errors', '1');
	ini_set('display_errors', '1');
}
if (CLI)
	cli_set_process_title('r3vCMS');

//redirect to init file
require_once ROOT.'/sys/_init.php';

\r3v\Mod::entrypoint();
