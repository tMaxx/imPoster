<?php ///revCMS /init/templates.php
///Various class and function templates

/**
 * Class loader function
 * @param $name class name
 * @todo everything
 */
function class__autoload($name)
{

}

spl_autoload_register('class__autoload');



/**
 * Log handling function
 * @param $msg message
 * @param $die to be or not to be
 */
function log($msg, $die = FALSE)
{
	$bt = debug_backtrace();
	$caller = array_shift($bt);

	$msg = '[Log] '.$caller['file'].'@'.$caller['line'].': '.$msg;

	if($die)
		die($msg);
	else
		echo $msg;
}



/**
 * "No instance" class
 * For every class that should not have an instance
 */
class NoInst
{
	function __construct()
	{	log('Thou shall not create a new object: '.get_class($this), TRUE); }
}



