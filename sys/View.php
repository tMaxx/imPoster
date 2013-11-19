<?php ///revCMS /sys/View.php
/**
* View class
*/
class View extends NoInst
{
	///Everything that wil be added in <head>here</head>
	private static $HTMLhead = array();
	///View mode: single, partial, full, recursive, ...
	static $workmode = NULL;

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
	public static function r($path, $params, $mode)
	{
		if(!CMS::fileExists('/view'.$path))
			throw new Exception('View "'.$path.'" does not exist');
	}

	/**
	 * Try to retrieve and render view, handle errors
	 * @param $path path from CMS
	 */
	public static function go($path)
	{
		if(self::$lockdown)
			return;

		parent::go();

		//TODO: Retrieve the path, generate view
		ob_start();

		switch (self::$mode) {
			case 'xyz_kończy_alfabet':
				die('bardzo śmieszne');
				break;
			default:
				View::r();
				break;
		}

		$body = ob_get_contents();
		ob_end_clean();
		self::template($body);
	}

	///Render footer
	public static function footer()
	{

	}

	/**
	 * Render template
	 * @param $body bod
	 */
	protected static function template()
	{
		if(CMS::fileExists('/templ/index.php'))
			;//include file
		else
			throw new Exception('View: template index.php not found!');
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

	///Render contents of $HTMLhead
	public static function head()
	{
		foreach (self::$HTMLhead as $v)
			echo $v;
	}

}
