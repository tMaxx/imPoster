<?php ///rev engine sys/_init.php
///Initialize CMS, add templates, run commands

/** dumper, uglier than ever */
function vdump() {
	if (!DEBUG) return;
	$wpre = (!CLI && (!function_exists('xdebug_get_code_coverage')));
	if ($wpre)
		echo '<pre>';
	elseif (class_exists('\\rev\\Console', false))
		$insp = rev\Console::inst('inspector');
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

/** Return milliseconds elapsed from init of script */
function ms_from_start() {
	return round(((microtime(true)*10000) - NOW_MICRO)/10, 2);
}

/** Return formatted date from unix timestamp */
function datef($unix_ts, $hrs = false) {
	return date(($hrs ? 'H:i ' : '').'d.m.Y', $unix_ts);
}

require_once ROOT.'/sys/rev/Errors.php';

set_exception_handler('\\rev\\Error::h');
set_error_handler('\\rev\\Error::h', E_ALL);

require_once ROOT.'/sys/rev/Mod.php';

spl_autoload_register('\\rev\\Mod::loadClass');
register_shutdown_function('\\rev\\Mod::unloadAll', 'shutdown');
\rev\Mod::sysinit(); //load sys definition

//this sets DEBUG constant, Conf will come in handy anyway
\rev\Mod::loadClass('rev\\Conf');

if (DEBUG) {
	error_reporting(E_ALL);
	ini_set('log_errors', '1');
	ini_set('display_errors', '1');
}

/* * * * * * * * * * * * * * * * * * * * * *
 * Framework includes for custom functions *
 * * * * * * * * * * * * * * * * * * * * * */

require_once ROOT.'/sys/rev/DB/_factory.php';
