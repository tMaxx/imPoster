<?php ///revCMS /sys/CMS.php
/**
 * CMS - basic content management class
 */
class CMS extends NoInst
{
	///CMS identificator
	const CMS_ID = 'revCMS codename /shadow/';
	///CMS version
	const CMS_VER = '0.1';
	///HTTP headers to be sent through header('here');
	private static $HTTPheaders = array();
	///GET parameters
	private static $GET = array();
	///POST parameters
	private static $POST = array();
	///Request path
	private static $PATH = array();

	///Perform any needed operations before executing any custom scripts
	///Just to keep it clean
	protected static function init()
	{
		if(self::locked())
			return;

		self::safeIncludeOnce('/sys/Errors.php');

		//revamp request to something more readable
		$ipath = explode('/', REQUEST);
		$ipath = (array) $ipath;

		//full request path
		$rpath = '';
		foreach($ipath as $k => $v) {
			if(empty($v) || !is_numeric($k))
				continue;
			//just the leftmost
			$t = explode(':', $v, 2);
			$t = (array) $t;
			$rpath .= '/'.$t[0];
			self::$PATH[$t[0]] = isset($t[1]) ? $t[1] : NULL;
		}
		self::$PATH[0] = $rpath;

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

		self::lockdown();
	}

	///Pre-exit commands
	public static function end()
	{
		if(!self::locked())
			return;
		self::lockup();
		DB::end();
	}

	///Run this house
	public static function go()
	{
		if(self::locked())
			return;

		self::init();

		View::go(self::$PATH);

		self::headers();
		self::end();
	}

	///Set all needed headers
	public static function headers()
	{
		foreach (self::$HTTPheaders as $v)
			header($v);
	}

	/**
	 * Add new header to be set later
	 * @param $header
	 */
	public static function addHeader($header)
	{
		if (!is_array($header))
			$header = array($header);

		foreach ($header as $k => $v) {
			if(!is_string($v))
				throw new Exception('Parameter is not a string!');
			if(is_numeric($k))
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
	public static function safeInclude($file)
	{
		if($r = self::fileExists($file))
			include ROOT.$file;
		return !!$r;
	}

	/**
	 * Includes file in param
	 * @param $file path to file, relative to /
	 * @return bool true on success
	 */
	public static function safeIncludeOnce($file)
	{
		if($r = self::fileExists($file))
			include_once ROOT.$file;
		return !!$r;
	}

	/**
	 * Check if $file exists in project
	 * @param $file path, relative to /
	 * @return bool
	 */
	public static function fileExists($file)
	{
		return file_exists(ROOT.$file);
	}

	/**
	 * Get value(s) from $_GET
	 * @param $in string|array
	 * string: single variable name
	 * array: values (as values, not keys), to be filled
	 * @return string|array
	 * string: single value
	 * array: returns filled array with variable names as keys
	 */
	public static function varGet($in)
	{
		if(is_array($in))
			foreach ($in as $k)
				$in[$k] = (isset(self::$GET[$k]) ? self::$GET[$k] : NULL);
		else
			$in = (isset(self::$GET[$in]) ? self::$GET[$in] : NULL);

		return $in;
	}

	/**
	 * Get value(s) from $_POST
	 * @param $in string|array
	 * string: single variable name
	 * array: values (as values, not keys), to be filled
	 * @return string|array
	 * string: single value
	 * array: returns filled array with variable names as keys
	 */
	public static function varPost($in)
	{
		if(is_array($in))
			foreach ($in as $k)
				$in[$k] = (isset(self::$POST[$k]) ? self::$POST[$k] : NULL);
		else
			$in = (isset(self::$POST[$in]) ? self::$POST[$in] : NULL);

		return $in;
	}

	/**
	 * Get value(s) from path
	 * @param $in string|array
	 * string: single variable name
	 * array: values (as values, not keys), to be filled
	 * @return string|array
	 * string: single value
	 * array: returns filled array with variable names as keys
	 */
	public static function varPath($in = 0)
	{
		if(is_array($in))
			foreach ($in as $k)
				$in[$k] = (isset(self::$PATH[$k]) ? self::$PATH[$k] : NULL);
		else
			$in = (isset(self::$PATH[$in]) ? self::$PATH[$in] : NULL);

		return $in;
	}

	/**
	 * Get absolute link to resource on server
	 * @param $target path
	 * @return string full path
	 */
	public static function l($target)
	{
		
	}

}
