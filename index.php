<?php ///revCMS /index.php
define('ROOT', realpath(dirname(__FILE__)));
define('CWD', getcwd());

define('REQUEST', $_GET['_req']);
unset($_GET['_req']);

//redirect to init file, nothing else to do here
include_once 'init/index.php';
die;
