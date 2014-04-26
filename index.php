<?php ///rev engine /index.php
//set charset
ini_set('default_charset', 'UTF-8');
mb_internal_encoding('UTF-8');
mb_regex_encoding('UTF-8');

//rev version
define('r3v_VERSION', '0.6beta1');
//rev identificator
define('r3v_ID', 'rev engine [dragons] v'.r3v_VERSION);

//time
define('NOW_MICRO', floor(microtime(true) * 10000));
define('NOW', time());

//location
define('ROOT', __DIR__);

//properties
define('AJAX', (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'));
define('CLI', (PHP_SAPI === 'cli'));
define('HOST', (CLI ? 'interactive' : ((empty($_SERVER['HTTPS']) ? 'http' : 'https').'://'.$_SERVER['HTTP_HOST'])));
define('REQUEST_URI', (CLI ? '/' : (explode('?', $_SERVER['REQUEST_URI'], 2)[0])));
define('PROCESS_ID', @posix_getpid());

if (CLI) {
	cli_set_process_title('r3v engine');
	define('NEWLINE', "\n"); //kinda sucks, but kinda works :D
} else
	define('NEWLINE', '<br>');

//redirect to init file
require_once ROOT.'/sys/_init.php';

\r3v\Mod::go();
