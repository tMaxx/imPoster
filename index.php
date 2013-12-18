<?php ///revCMS /index.php
define('ROOT', realpath(dirname(__FILE__)));
if (!defined('DEBUG')) {
	if (getenv('r3vDEBUG') == 'TRUE')
		define('DEBUG', TRUE);
	else
		define('DEBUG', FALSE);
}

//redirect to init file, nothing else to do here
require_once ROOT.'/init/index.php';
