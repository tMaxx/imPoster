<?php ///revCMS /index.php
define('ROOT', realpath(dirname(__FILE__)));
if (!defined('DEBUG')) {
	if (strtolower(getenv('r3vDEBUG')) == 'true')
		define('DEBUG', TRUE);
	else
		define('DEBUG', FALSE);
}

//redirect to init file, nothing else to do here
require_once ROOT.'/sys/_init.php';
