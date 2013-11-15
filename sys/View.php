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
	static function viewExists($path)
	{
		return Cms::fileExists('/view'.$path);
	}

	/**
	 * Render view specified in param
	 * @param $path relative to /view
	 */
	static function r($path)
	{
		Cms::r('/view'.$path);
	}

}