<?php
//Init script - run all neccessary commands here, before passing control to UI
define('NOW', time());
define('HOST', $_SERVER['HTTP_HOST']);

try {
	CMS::go();
} catch(Exception $e) {
	echo revCMS_error_pretty_print($e);
}
