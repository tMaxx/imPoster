<?php ///revCMS init/index.php
///Initialize CMS, add templates, run commands

/**
 * "No instance" class
 * For every class that should not have an instance
 */
class NoInst
{
	protected static $lockdown = FALSE;

	function __construct()
	{	throw new Error('Thou shall not create a new object!'); }

	static function init()
	{	self::$lockdown = TRUE; }
}

/**
 * Log handling function
 * @param $ath message
 * @param $die to be or not to be
 */
function dlog($ath, $die = FALSE)
{
	$caller = array_shift(debug_backtrace());
	$msg = '[Log] '.$caller['file'].'@'.$caller['line'].': '.$msg;

	var_dump($ath);
	if($die)
		die($msg);
	else
		echo $msg;
}

/**
 * Anything logging function
 * @param $msg whatever
 * @param $die to be or not to be
 */
function rlog($msg, $die = FALSE)
{
	$caller = debug_backtrace();
	$caller = array_shift($caller);
	$m = '[Log] '.$caller['file'].'@'.$caller['line'].': '.$msg;

	if($die)
		die($msg);
	else
		echo $msg;
}

/**
 * Return trimmed dirs in string
 * @param $str
 * @return trimmed ROOT
 */
function pathdiff($str)
{
	return str_replace(ROOT, '', $str);
}

/**
 * Format error to something readable
 * @param $e Error/Exception
 */
function revCMS_e_pretty_print($e)
{
	$result = array('<br /><br />');

	if($e instanceof Error || $e instanceof ErrorException)
		$result[] = '<big><b>Error</b></big>: ';
	elseif($e instanceof Exception)
		$result[] = '<big><b>Exception</b></big>: ';
	else
		die('FATAL: programmer is an instanceof PEBKAC');

	$result[] = $e->getMessage();
	$result[] = ' at func ';
	$trace = $e->getTrace();
	if(isset($trace[1]['class'])) {
	  $result[] = $trace[1]['class'];
	  $result[] = $trace[1]['type'];
	}
	$result[] = $trace[1]['function'];
	$result[] = '();<br />Full stack trace:<br />';
	$result[] = nl2br(pathdiff(preg_replace('/^.+\n/', '', $e->getTraceAsString())));

	echo implode('', $result);
}

set_exception_handler('revCMS_e_pretty_print');

/**
 * Custom error handler
 * @param standard params
 */
function revCMS_error_handler($errno, $errstr, $errfile, $errline, $errcontext)
{
	static $constants;
	if(!isset($constants)){
		$constants = get_defined_constants(1);
		$constants = $constants['Core'];
	}

	$eName = 'Unknown error type';
	foreach ($constants as $key => $value) {
		if (substr($key, 0, 2) == 'E_' && $errno == $value) {
			$eName = $key;
			break;
		}
	}

	$msg = $eName.': '.$errstr.' in '.$errfile.' line '.$errline;

	revCMS_e_pretty_print(new ErrorException($msg));
}

set_error_handler('revCMS_error_handler', E_ALL);

/**
 * Custom error handler
 * @param standard params
 */
function revCMS_fatal_handler()
{
	$error = error_get_last();

	if($error !== NULL) {
		$errno	 = $error['type'];
		$errfile = $error['file'];
		$errline = $error['line'];
		$errstr	= '<b>FATAL</b>: '.$error['message'];
		revCMS_error_handler($errno, $errstr, $errfile, $errline, NULL);
	}
}

register_shutdown_function('revCMS_fatal_handler');

/**
 * Class loader function
 * @param $class class name
 * @todo everything
 */
function revCMS_class_autoload($class)
{
	static $sysTab;
	if(!isset($sysTab))
		$sysTab = array_diff(scandir(ROOT.'/sys/'), array('..', '.'));

	$classp = $class . '.php';

	if(in_array($classp, $sysTab))
		require_once ROOT.'/sys/'.$classp;
	elseif(file_exists(ROOT.'/app/'.$classp))
		require_once ROOT.'/app/'.$classp;
	else
		throw new Error('Class not found: '.$class);
}

spl_autoload_register('revCMS_class_autoload');

//get config
include_once 'config.php';

define('NOW', time());
define('HOST', $_SERVER['HTTP_HOST']);

CMS::go();
