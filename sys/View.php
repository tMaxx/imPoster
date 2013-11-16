<?php
/**
* View class
*/
class View extends NoInst
{
	/**
	 * Check if view exists
	 * @param $path relative to /view
	 * @return bool
	 */
	private static function viewExists($path)
	{
		return CMS::fileExists('/view'.$path);
	}

	/**
	 * Render view specified in param
	 * @param $path relative to /view
	 */
	public static function r($path)
	{
		if(!CMS::safeInclude($path))
			throw new Exception('View "'.$path.'" does not exist');
	}

}