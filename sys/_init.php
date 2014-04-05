<?php ///r3v engine sys/_init.php
///Initialize CMS, add templates, run commands

/** dumper, uglier than ever */
function vdump() {
	if (!DEBUG) return;
	$wpre = (!CLI && (!function_exists('xdebug_get_code_coverage')));
	if ($wpre)
		echo '<pre>';
	elseif (class_exists('\\r3v\\Console', false))
		$insp = r3v\Console::inst('inspector');
	foreach (func_get_args() as $v) {
		if (CLI)
			echo $insp->inspect($v);
		else
			var_dump($v);
		echo NEWLINE;
	}
	if ($wpre)
		echo '</pre>';
}

/** clone array, w. dereferencing */
function array_copy(array $source) {
	$arr = array();

	foreach ($source as $k => $el)
		if (is_array($el))
			$arr[$k] = array_copy($el);
		elseif (is_object($el))
			// make an object copy
			$arr[$k] = clone $el;
		else
			$arr[$k] = $el;
	return $arr;
}

///pop last el. of array as [key => value]
///probably duplicate
function array_popk(array &$arr) {
	if (!$arr)
		return [];
	end($arr);
	$k = key($arr);
	$v = array_pop($arr);
	return [$k => $v];
}

/** Return milliseconds elapsed from init of script */
function ms_from_start() {
	return round(((microtime(true)*10000) - NOW_MICRO)/10, 2);
}

require_once ROOT.'/sys/r3v/Errors.php';

set_exception_handler('\\r3v\\Error::h');
set_error_handler('\\r3v\\Error::h', E_ALL);

require_once ROOT.'/sys/r3v/Mod.php';

spl_autoload_register('\\r3v\\Mod::loadClass');
register_shutdown_function('\\r3v\\Mod::unloadAll', 'shutdown');
\r3v\Mod::sysinit(); //load sys definition

//set DEBUG constant, Conf will come in handy anyway
\r3v\Mod::loadClass('\\r3v\\Conf');

if (DEBUG) {
	error_reporting(E_ALL);
	ini_set('log_errors', '1');
	ini_set('display_errors', '1');
}

/////////////////////////////////////////////////////////////////////
// Class-specific functions
/////////////////////////////////////////////////////////////////////

///DB factory
function DB($var) {
	if (is_object($var) && ($var instanceof r3v\DB\Saveable))
		return new r3v\DB\Instance($var);
	elseif (is_string($var)) {
		if (substr_count($var, ' ') == 0)
			return new r3v\DB\Table($var);
		return new r3v\DB\Base($var);
	} else
		throw new r3v\DB\Error('Unsupported $var type');
}
