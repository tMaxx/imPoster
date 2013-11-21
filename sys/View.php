<?php ///revCMS /sys/View.php
/**
 * View/HTML class
 */
class View extends NoInst
{
	///Main template used
	const TEMPLATE = '/templ/index.php';
	///Everything that wil be added in <head>here</head>
	private static $HTMLhead = array();
	///Working mode
	///0: muted, 1: single, 2-n: recursive
	private static $workmode = 0;

	/**
	 * Check if view exists
	 * @param $path relative to /view
	 * @return bool
	 */
	private static function viewExists($path)
	{
		//FIXME: path can be really weird
		//ensure it's good to go
		if(empty($path) || $path == '/')
			return true;
		return CMS::fileExists('/view'.$path);
	}

	/**
	 * Render view specified in param
	 * @param $path relative to /view
	 * @param $params additional
	 * @param $mode render mode
	 */
	public static function r($path, array $params = array(), $mode = 0)
	{
		if(!View::viewExists($path))
			throw new ErrorHTTP('View "'.$path.'" does not exist', 404);

		switch ($mode)
		{
			case 0:
				//muted
				//break;
			case 1:
				//single
				//break;
			case 2:
				//single w. index
				//break;
			case 3:
			case 4:
				//levels
				//break;
			default:
				//full render
				ob_flush();
				if(!CMS::safeIncludeOnce(self::TEMPLATE))
					throw new ErrorHTTP('Template '.self::TEMPLATE.' not found', 404);
				break;
		}

	}

	/**
	 * Try to retrieve and render view, handle errors
	 * @param $path path from CMS
	 */
	public static function go($path)
	{
		if(self::locked())
			return;
		self::lockdown();

		//FIXME: request path processing
		//real files, not nodes

		ob_start();
		if(is_int(self::$workmode) && self::$workmode >= 0)
			View::r($path[0], array(), self::$workmode);
		else
			throw new ErrorHTTP('View: Unsupported working mode!', 400);

		$body = ob_get_contents();
		ob_end_clean();
		CMS::headers();
		echo $body;
	}

	///Render footer
	public static function footer()
	{
		echo 'nopenopenope';
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

	///Wrapper for renders
	public static function body()
	{
		return 'zawartość';
	}

	///Return site title, based on whatever needed
	public static function title()
	{
		return 'codename teo';
	}

}
