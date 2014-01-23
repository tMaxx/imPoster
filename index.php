<?php ///r3vCMS /index.php
//set charset
ini_set('default_charset', 'UTF-8');
mb_internal_encoding('UTF-8');
mb_regex_encoding('UTF-8');

//time
define('NOW_MICRO', (int)(microtime(true) * 10000));
define('NOW', time());

//location
define('HOST', $_SERVER['HTTP_HOST']);
define('ROOT', realpath(dirname(__FILE__)));

//properties
define('AJAX', (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'));
define('CLI', FALSE); //FIXME //NOPE //MAYBE SOMEDAY
if (!defined('DEBUG')) {
	if (strtolower(getenv('r3vDEBUG')) == 'true')
		define('DEBUG', TRUE);
	else
		define('DEBUG', FALSE);
}

//redirect to init file, nothing else to do here
require_once ROOT.'/sys/_init.php';
