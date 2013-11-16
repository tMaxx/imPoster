<?php ///revCMS /index.php
define('ROOT', realpath(dirname(__FILE__)));
define('CWD', getcwd());

define('REQUEST', $_GET['__req__']);
unset($_GET['__req__']);

//redirect to init file, nothing else to do here
include_once ROOT.'/init/index.php';
