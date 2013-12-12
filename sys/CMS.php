<?php ///revCMS /sys/CMS.php
/**
 * CMS - basic content management class
 */
class CMS extends _Locks {
	///CMS identificator
	const CMS_ID = 'revCMS codename /shadow/';
	///CMS version
	const CMS_VER = '0.3';
	///HTTP headers to be sent through header('here');
	private static $HTTPheaders = array();
	///GET parameters
	private static $GET = array();
	///POST parameters
	private static $POST = array();
	///URI parameters & exploded path
	private static $URI = array();

	///Perform any needed operations before executing any custom scripts
	///Just to keep it clean
	protected static function init() {
		if (self::lock())
			return;

		self::safeIncludeOnce('/sys/Errors.php');

		//revamp request to something more readable
		$ipath = (array) explode('/', $_GET['__req__']);
		unset($_GET['__req__']);
		$rpath = '/';
		self::$URI[1] = array();
		foreach ($ipath as $k => $v) {
			if (empty($v) || !is_numeric($k))
				continue;
			$e = (array) explode(':', $v, 2);
			$rpath .= $e[0];
			self::$URI[1][] = $e[0];
			if (isset($e[1]))
				self::$URI[$e[0]] = $e[1];
			$rpath .= '/';
		}
		self::$URI[0] = substr($rpath, 0, -1);

		//set variables
		self::$GET = $_GET;
		self::$POST = $_POST;
		unset($_GET, $_POST);

		self::$HTTPheaders = array(
			'content-type' => 'Content-Type: text/html; charset=utf-8',
			'X-Powered-By: monkeys on bikes',
			'X-Backend: '.self::CMS_ID.' '.self::CMS_VER,
		);

		global $SQL_CONNECTION;

		//config DB, clear config
		DB::go($SQL_CONNECTION);
		unset($GLOBALS['SQL_CONNECTION']);
	}

	///Pre-exit commands
	public static function end() {
		if (self::lock())
			return;
		DB::end();
	}

	///Run this house
	public static function go() {
		if (self::lock())
			return;

		self::init();

		View::go(self::$URI);

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
	 * Check if this type is allowed in here
	 * @param $type
	 */
	public static function guard_allowedVarTypes($type) {
		if (!in_array($type, array('POST', 'GET', 'URI')))
			throw new ErrorCMS('Invalid var $type');
	}

	/**
	 * Get isset's return value
	 * @param $type POST, GET, URI
	 * @param $var name
	 * @return bool
	 */
	public static function varIsSet($type, $in) {
		self::guard_allowedVarTypes($type);

		if (is_array($in)) {
			foreach ($in as $v)
				if (isset(self::${$type}[$v])) {
					return true;
					break;
				}
			return false;
		} else
			return isset(self::${$type}[$in]);
	}

	/**
	 * Get value(s) from self::${$type}
	 * @param $type POST, GET, URI
	 * @param $in string|array
	 * string: single variable name
	 * array: values (as values, not keys), to be filled
	 * @param $unset bool unset variable(s) from $in
	 * @return string|array
	 * string: single value
	 * array: returns filled array with variable names as keys
	 */
	public static function vars($type, $in, $ifnset = NULL, $unset = FALSE) {
		self::guard_allowedVarTypes($type);

		if (is_array($in)) {
			$r = array();
			foreach ($in as $v) {
				$r[$v] = isset(self::${$type}[$v]) ? self::${$type}[$v] : $ifnset;
				if ($unset)
					unset(self::${$type}[$v]);
			}
		} elseif (is_string($in)) {
			$r = isset(self::${$type}[$in]) ? self::${$type}[$in] : $ifnset;
			if ($unset)
				unset(self::${$type}[$in]);
		} else
			throw new Error('Unsupported $var type; only array or string');

		return $r;
	}

	/**
	 * Get absolute link to resource on server
	 * @param $target path
	 * @return string full path
	 */
	public static function l($target) {
		///TODO
	}
}
