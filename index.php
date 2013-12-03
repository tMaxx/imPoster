<?php ///revCMS /index.php
define('NOW_MICRO', microtime(true));
define('ROOT', realpath(dirname(__FILE__)));
define('DEBUG', TRUE);

//redirect to init file, nothing else to do here
require_once ROOT.'/init/index.php';
