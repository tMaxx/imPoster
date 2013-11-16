<?php ///revCMS init/index.php
///Initialize CMS, run commands
include_once './templates.php';
include_once './config.php';

define('NOW', time());
define('HOST', $_SERVER['HTTP_HOST']);

CMS::go();
