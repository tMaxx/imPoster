<?php
/**
* Cms - basic content management class
*/
class Cms
{
	//self
	private $single = NULL;
	//Db: database object
	private $db = NULL;
	
	function __construct()
	{
		return;
	}

	/**
	 * Perform any needed operations before executing any custom scripts
	 */
	public static function init()
	{

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
		
	}

	/**
	 * Includes file in param
	 * MUST NOT BE FROM /view!
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

	}

	/**
	 * Gets a file from /view and renders it onto screen
	 * @param $view path, relative to /view (e: 'user/index')
	 */
	public static function render($view)
	{
		
	}

}
