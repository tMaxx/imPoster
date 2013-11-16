<?php ///revCMS /init/templates.php
///Various class and function templates


/**
 * Format error to something readable
 */
function revCMS_e_pretty_print($e)
{
	$trace = $e->getTrace();

	if($e instanceof Error)
		$result = 'Error: "';
	elseif($e instanceof Exception)
		$result = 'Exception: "';
	else
		throw new Error("Param $e is neither an exception nor error! WTH are you doing??");

	$result .= $e->getMessage();
	$result .= '" @ ';
	if($trace[0]['class']) {
	  $result .= $trace[0]['class'];
	  $result .= $trace[0]['type'];
	}
	$result .= $trace[0]['function'];
	$result .= '('.$trace[0]['args'].');<br />';

	return $result;
}

/**
 * Custom error handler
 * @params standard params
 */
function revCMS_error_handler($errno, $errstr, $errfile, $errline, $errcontext)
{
    $constants = get_defined_constants(1);

    $eName = 'Unknown error type';
    foreach ($constants['Core'] as $key => $value) {
        if (substr($key, 0, 2) == 'E_' && $errno == $value) {
            $eName = $key;
            break;
        }
    }

    $msg = $eName . ': ' . $errstr . ' in ' . $errfile . ', line ' . $errline;

    throw new Exception($msg);
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
		throw new Exception('Class not found: '.$class);
}

spl_autoload_register('revCMS_class_autoload');

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

	public static function init() {}
}



