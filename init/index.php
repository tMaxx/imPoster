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

	function lockdown()
	{	return self::$lockdown = TRUE; }
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
 * Custom all-error handler
 * @param standard params
 */
function revCMS_e_handler($eno = NULL, $estr = NULL, $efile = NULL, $eline = NULL, $econtext = NULL)
{
	static $constants;
	if(!isset($constants)){
		$constants = get_defined_constants(1);
		$constants = $constants['Core'];
	}

	$trace = debug_backtrace();

	$result = array('<br /><br />');

	if((isset($eno) && isset($estr) && isset($efile)) || (!isset($eno) && (($e_last = error_get_last()) !== NULL))) //error
	{
		$eName = '?';

		if(isset($e_last['type']))
			$eno = $e_last['type'];

		foreach ($constants as $key => $value)
			if (substr($key, 0, 2) == 'E_' && $eno == $value) {
				$eName = $key;
				unset($trace[0]);
				break;
			}

		if(isset($e_last['message']))
		{
			$eName = '<b>FATAL</b>: '.$eName;
			$efile = $e_last['file'];
			$eline = $e_last['line'];
			$estr = $e_last['message'];
		}

		$result[] = '<big><b>Error</b></big>: '.$eName.': '.$estr.' in '.pathdiff($efile).' line '.$eline;
	}
	elseif(isset($eno)) //exception handler
	{
		if($eno instanceof Error || $eno instanceof ErrorException)
			$result[] = '<big><b>Error</b></big>: ';
		elseif($eno instanceof Exception)
			$result[] = '<big><b>Exception</b></big>: ';

		$result[] = $eno->getMessage();
		$result[] = ' in file ';
		$result[] = pathdiff($eno->getFile());
		$result[] = ' line ';
		$result[] = $eno->getLine();

		$trace = $eno->getTrace();
	}
	else
		if(!isset($e_last))
			CMS::end();

	$result[] = '<br />Stack trace:<br />';

	if(!count($trace))
		$result[] = '<i>No stack trace available</i><br />';

	//FIXME: $trace may not have all values set
	foreach($trace as $i => $v)
	{
		$result[] = $i . '# ';
		if(isset($v['file']) && $v['file'])
		{
			$result[] = pathdiff($v['file']);
			$result[] = ':';
			$result[] = $v['line'];
			$result[] = ' - ';
		}
		else
			$result[] = '[<i>internal call</i>] ';
		if(isset($v['class']))
		{
			$result[] = $v['class'];
			$result[] = $v['type'];
		}
		$result[] = $v['function'];
		$result[] = '()';
		if(isset($v['args']) && $v['args']) {
			$result[] = ', args: ';
			$result[] = strip_tags(json_encode($v['args']));
		}
		$result[] = '<br />';
	}

	echo implode('', $result);
}

set_exception_handler('revCMS_e_handler');

set_error_handler('revCMS_e_handler', E_ALL);

register_shutdown_function('revCMS_e_handler');

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
