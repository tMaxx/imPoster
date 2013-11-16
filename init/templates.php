<?php ///revCMS /init/templates.php
///Various class and function templates

/**
 * Format error to something readable
 * @param $e Error/Exception
 */
function revCMS_e_pretty_print($e)
{
	$result = array();

	if($e instanceof Error || $e instanceof ErrorException)
		$result[] = 'Error: "';
	elseif($e instanceof Exception)
		$result[] = 'Exception: "';
	else
		die("FATAL: programmer is an instanceof PEBKAC");

	$trace = $e->getTrace();
	$result[] = $e->getMessage();
	$result[] = '" @ ';
	if($trace[0]['class']) {
	  $result[] = $trace[0]['class'];
	  $result[] = $trace[0]['type'];
	}
	$result[] = $trace[0]['function'];
	$result[] = '();<br />Full stack trace:<br />';
	$result[] = $e->getTraceAsString();

	echo implode('', $result);
}

set_exception_handler('revCMS_e_pretty_print');

/**
 * Custom error handler
 * @param standard params
 */
function revCMS_error_handler($errno, $errstr, $errfile, $errline, $errcontext)
{
    static $constants = get_defined_constants(1);

    $eName = 'Unknown error type';
    foreach ($constants['Core'] as $key => $value) {
        if (substr($key, 0, 2) == 'E_' && $errno == $value) {
            $eName = $key;
            break;
        }
    }

    $msg = $eName . ': ' . $errstr . ' in ' . $errfile . ', line ' . $errline;

    revCMS_e_pretty_print(new Error($msg));
}

set_error_handler('revCMS_error_handler', E_ALL);

/**
 * Class loader function
 * @param $class class name
 * @todo everything
 */
function revCMS_class_autoload($class)
{
	const sysTab = array_diff(scandir(ROOT.'/sys/'), array('..', '.'));

	$class .= ;

	if(in_array($class.'.php', sysTab)){
		require_once ROOT.'/sys/'.$class.'.php';
		$class::init();
	} elseif(CMS::fileExists('/app/'.$class.'.php'))
		require_once ROOT.'/app/'.$class.'.php';
	else
		throw new Error('Class not found: '.$class);
}

spl_autoload_register('revCMS_class_autoload');

/**
 * Log handling function
 * @param $msg message
 * @param $die to be or not to be
 */
function log($msg, $die = FALSE)
{
	$caller = array_shift(debug_backtrace());
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
	private static $lockdown = FALSE;

	function __construct()
	{	throw new Error('Thou shall not create a new object!'); }

	public static function init() {}
}
