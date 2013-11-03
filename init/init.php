<?php
//Init script - run all neccessary commands here, before passing control to UI
define('NOW', time());
define('HOST', $_SERVER['HTTP_HOST']);

CMS::init();
DB::init();


