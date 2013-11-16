<?php
/**
 * CMS - basic content management class
 */
class CMS extends NoInst
{
	///HTTP headers to be sent through header('here');
	private static $HTTPheaders = array();
	///Everything that wil be added in <head>here</head>
	private static $HTMLhead = array();
	///GET parameters
	private static $GET = array();
	///POST parameters
	private static $POST = array();
	///Request path
	private static $path = '';

	/**
	 * Perform any needed operations before executing any custom scripts
	 */
	private static function init()
	{
		$tpath = explode('/', $_GET['_req']);

		unset($_GET['_req']);

		//config DB, clear config
		DB::connect($SQL_CONNECTION);

		//set variables
		self::$GET = $_GET;
		self::$POST = $_POST;

		//clean up
		unset($SQL_CONNECTION, $_GET, $_POST);
	}

	/**
	 * Run this house
	 */
	private static function go()
	{
		self::init();
	
		//TODO: Retrieve the path, generate view
		ob_start();

		View::r();

		$body = ob_get_contents();
		ob_end_clean();
		self::template($body);

		self::headers();
	}

	/**
	 * Set all needed headers
	 */
	public static function headers()
	{
		foreach (self::$HTTPheaders as $v) {
			header($v);
		}
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
	 * Add new header to be set later
	 * @param $html
	 */
	public static function addToHead($html)
	{
		if (!is_array($header))
			$header = array($header);

		foreach ($header as $v) {
			if(!is_string($v))
				throw new Exception('Parameter is not a string!');
			self::$HTMLhead[] = $v;
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
		return $r;
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
		return $r;
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
	 * Return $path variable
	 * @return string
	 */
	public static function getPath()
	{
		return self::$path;
	}

}
