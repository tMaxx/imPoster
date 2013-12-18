<?php ///revCMS init/index.php
///Initialize CMS, add templates, run commands

/**
 * Implements locks for every class
 */
class _Locks {
	protected static $LOCKS = array();

	///Class function - is locked?
	final protected static function is_locked($n = 2) {
		$r = isset(self::$LOCKS[($c = get_called_class())][($f = debug_backtrace()[$n]['function'])]);
		return array($r, $c, $f);
	}

	///Set the lock, but 1st time return false
	final protected static function lock() {
		list($r, $c, $f) = self::is_locked();
		return $r ? TRUE : (!(self::$LOCKS[$c][$f] = TRUE));
	}

	///Unset the lock, but 1st time return false
	final protected static function unlock() {
		list($r, $c, $f) = self::is_locked();
		if ($r)
			self::$LOCKS[$c][$f] = NULL;
		return !$r;
	}
}

///dumper
function pre_dump() {
	$vars = func_get_args();
	echo '<pre>';
	foreach ($vars as $v) {
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
function pathdiff($str) {
	static $ar;
	if (!$ar)
		$ar = str_replace('/', '\/', ROOT);
	return str_replace(array(ROOT, $ar), '', $str);
}

//get config
include_once 'config.php';

define('NOW_MICRO', microtime(true));
define('NOW', time());
define('HOST', $_SERVER['HTTP_HOST']);
define('AJAX', (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'));

require_once ROOT.'/sys/Errors.php';

set_exception_handler('Error::h');
set_error_handler('Error::h', E_ALL);
register_shutdown_function('Error::h');

require_once ROOT.'/sys/CMS.php';

spl_autoload_register('CMS::class_load');

CMS::go();
