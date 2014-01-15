<?php ///r3vCMS /sys/CMS.php
namespace {
/**
 * CMS - basic content management class
 */
class CMS extends _Locks {
	///CMS identificator
	const CMS_ID = 'revCMS codename /shadow/';
	///CMS version
	const CMS_VER = '0.4';
	///HTTP headers to be sent through header('here');
	private static $HTTPheaders = array();

	///Perform any needed operations before executing any custom scripts
	///Just to keep it clean
	protected static function init() {
		if (self::lock())
			return;

		self::$HTTPheaders = array(
			'X-Powered-By: monkeys on bikes',
			'X-Backend: '.self::CMS_ID.' '.self::CMS_VER,
		);

		new CMS\Vars();

		global $SQL_CONNECTION;

		//config DB, clear config
		CMS\DB\Base::go($SQL_CONNECTION);
		unset($GLOBALS['SQL_CONNECTION']);

		CMS\Me::autoload();
	}

	///Pre-exit commands
	public static function end() {
		if (self::lock())
			return;

		CMS\Session::save();
		CMS\DB\Base::end();
		self::flushHeaders();
	}

	///Run this house
	public static function go() {
		if (self::lock())
			return;

		self::init();

		if (!CMS\Mod::checkServices())
			CMS\View::go();

		self::end();
	}

	///Set all needed headers
	public static function flushHeaders() {
		foreach (self::$HTTPheaders as &$v) {
			header($v);
			unset($v);
		}
	}

	/**
	 * Add new header to be set later
	 * @param $header
	 */
	public static function addHeader($header) {
		if (!is_array($header))
			$header = array($header);

		foreach ($header as $k => $v) {
			if (!is_string($v))
				throw new Exception('Parameter is not a string!');
			if (is_numeric($k))
				self::$HTTPheaders[] = $v;
			else
				self::$HTTPheaders[$k] = $v;
		}
	}

	/**
	 * Set content-type header
	 * @param $type
	 * @return bool success
	 */
	public static function setContentType($type) {
		switch (strtolower($type)) {
			case 'pdf':
				$type = 'application/css';
				break;
			case 'json':
				$type = 'application/json';
				break;
			case 'text':
				$type = 'text/plain';
				break;
			case 'html':
				$type = 'text/html';
				break;
			case 'css':
				$type = 'text/css';
				break;
			case 'jpeg':
				$type = 'jpeg';
				break;
			case 'gif':
				$type = 'image/gif';
				break;
			case 'png':
				$type = 'image/png';
				break;
			default:
				return false;
				break;
		}
		self::$HTTPheaders['content-type'] = 'Content-Type: '.$type.'; charset=utf-8';
		return true;
	}

	/**
	 * Includes file in param
	 * @param $file path to file, relative to /
	 * @return bool true on success
	 */
	public static function safeInclude($file) {
		if ($r = self::fileExists($file))
			include ROOT.$file;
		return !!$r;
	}

	/**
	 * Includes file in param
	 * @param $file path to file, relative to /
	 * @return bool true on success
	 */
	public static function safeIncludeOnce($file) {
		if ($r = self::fileExists($file))
			include_once ROOT.$file;
		return !!$r;
	}

	/**
	 * Check if file or directory exists on server
	 * @param $node path, relative to /
	 * @return bool
	 */
	public static function nodeExists($node) {
		return file_exists(ROOT.$file);
	}

	/**
	 * Check if $file exists in project
	 * @param $file path, relative to /
	 * @return bool
	 */
	public static function fileExists($file) {
		return is_file(ROOT.$file);
	}

	/**
	 * Check if $dir exists in project and is not a file
	 * @param $dir path, relative to /
	 * @return bool
	 */
	public static function dirExists($dir) {
		return is_dir(ROOT.$dir);
	}

	/**
	 * Check if class file exists
	 * @param $name class name
	 * @return bool
	 */
	public static function appClassExists($name) {
		return self::fileExists('/app/'.$name.'.php');
	}

	/**
	 * Returns an array retrieved from json file from path
	 * @param $path relative to /
	 * @return false|array
	 */
	public static function jsonFromFile($path) {
		return CMS\Mod::jsonFromFile($path);
	}

	/**
	 * Return scandir() without dots
	 * @param $dir ectory
	 * @return array
	 */
	public static function scandir($dir) {
		return array_diff(scandir(ROOT.$dir), array('.', '..'));
	}

	/**
	 * Return path with any dots, slashes and tildes removed
	 * @param $path
	 * @return string
	 */
	public static function sanitizePath($path) {
		return str_replace(array('.', '/', '~'), '', $path);
	}

	/**
	 * Get absolute link to resource on server
	 * @param $target path
	 * @return string full path
	 */
	public static function l($target) {
		throw new Error501();
	}
}
}
namespace CMS {
class CMS extends \CMS {}
}
