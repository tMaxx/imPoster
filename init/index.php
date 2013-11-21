<?php ///revCMS init/index.php
///Initialize CMS, add templates, run commands

/**
 * "No instance" class
 * For every class that should not have an instance
 * Implements locks for every class
 */
class NoInst
{
	protected static $LOCKS = array();

	final function __construct()
	{	throw new Error('Thou shall not create a new object!'); }

	///Class function - is locked?
	final protected static function is_locked($n = 2)
	{
		$r = isset(self::$LOCKS[($c = get_called_class())][($f = debug_backtrace()[$n]['function'])]);
		return array($r, $c, $f);
	}

	///Set the lock, but 1st time return false
	final protected static function lock()
	{
		list($r, $c, $f) = self::is_locked();
		return $r ? TRUE : (!(self::$LOCKS[$c][$f] = TRUE));
	}

	///Unset the lock, but 1st time return false
	final protected static function unlock()
	{
		list($r, $c, $f) = self::is_locked();
		if($r)
			self::$LOCKS[$c][$f] = NULL;
		return !$r;
	}
}

///dumper
function pre_dump()
{
	$vars = func_get_args();
	echo '<pre>';
	foreach ($vars as $v){
		var_dump($v);
		echo '<br />';
	}
	echo '</pre>';
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

	if((isset($eno, $estr, $efile)) || (!isset($eno) && (($e_last = error_get_last()) !== NULL))) //error
	{
		ob_get_clean();
		$eName = '?';

		if(isset($e_last['type']))
			$eno = $e_last['type'];

		foreach ($constants as $key => $value)
			if (substr($key, 0, 2) == 'E_' && $eno == $value)
			{
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

		$result[] = '<big><b>Error</b></big>: '.$eName.': '.$estr.' at '.pathdiff($efile).':'.$eline;
	}
	elseif(isset($eno)) //exception handler
	{
		ob_get_clean();
		if($eno instanceof Error)
			$result[] = '<big><b>Error</b></big>: ';
		elseif($eno instanceof Exception)
			$result[] = '<big><b>Exception</b></big>: ';
		elseif($eno instanceof ErrorException)
			$result[] = '<big><b>Error/Exception</b></big>: ';

		$result[] = $eno->getMessage();
		$result[] = ' at ';
		$result[] = pathdiff($eno->getFile());
		$result[] = ':';
		$result[] = $eno->getLine();

		$trace = $eno->getTrace();
	}
	else
	{
		if(!isset($e_last))
			CMS::end();
		return;
	}

	$result[] = '<br />Stack trace:<br />';

	if(!count($trace))
		$result[] = '<i>No stack trace available</i><br />';
	else
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
		if(isset($v['args']) && $v['args'])
		{
			$result[] = ', args: ';
			$result[] = htmlspecialchars(json_encode($v['args']), ENT_COMPAT|ENT_HTML5);
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
		$sysTab = array_diff(scandir(ROOT.'/sys/'), array('..', '.', 'Errors.php'));

	$classp = $class . '.php';

	if(in_array($classp, $sysTab))
		require_once ROOT.'/sys/'.$classp;
	elseif(file_exists(ROOT.'/app/'.$classp))
		require_once ROOT.'/app/'.$classp;
	else
		throw new ErrorException('Class not found: '.$class);
}

spl_autoload_register('revCMS_class_autoload');

//get config
include_once 'config.php';

define('NOW', time());
define('HOST', $_SERVER['HTTP_HOST']);

CMS::go();
