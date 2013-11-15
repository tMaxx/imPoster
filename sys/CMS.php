<?php
/**
 * CMS - basic content management class
 */
class CMS extends NoInst
{
	//HTTP headers to be sent through header('here');
	private static $HTTPheaders = array();
	//everything that wil be added in <head>here</head>
	private static $HTMLhead = array();

	/**
	 * Perform any needed operations before executing any custom scripts
	 */
	public static function init()
	{
		self::headers();
	}

	/**
	 * Set all needed headers
	 */
	public static function headers()
	{
		
	}

	/**
	 * Add new header to be set later
	 * @param $header
	 */
	public static function addHeader($header)
	{
		if (!is_array($header))
			$header = array($header);

		foreach ($header as $v) {
			if(!is_string($v))
				throw new Exception('$header: not a string!');
			self::$HTTPheaders[] = $v;
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
				throw new Exception('$html: not a string!');
			self::$HTMLhead[] = $v;
		}
	}
	
	/**
	 * Includes file in param
	 * @param $file path to file, relative to /
	 * @return bool true on success
	 */
	public static function incFile($file)
	{

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
	 * Gets a file from /view and renders it onto screen
	 * @param $view path, relative to /view (e: 'user/index')
	 */
	/*public static function render($view)
	{//moved to View
		
	}*/

}
