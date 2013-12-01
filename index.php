<?php ///revCMS /index.php
define('NOW_MICRO', microtime(true));
define('ROOT', realpath(dirname(__FILE__)));

define('MODE', isset($_GET['__mode__']) ? $_GET['__mode__'] : 'FULL');
unset($_GET['__mode__']);

//redirect to init file, nothing else to do here
require_once ROOT.'/init/index.php';
