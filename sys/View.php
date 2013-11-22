<?php ///revCMS /sys/View.php
///Actually just a complete view generator
class ViewRequiringDiscoverer
{
	///Cursor
	protected $c = '';
	///File
	protected $f = '';
	///Next node
	protected $n = '';
	///Limit
	protected $l = 13;
	///Iterator - current depth
	protected $i = 0;
	///Upper (parent) object
	protected $p = NULL;
	//Created objects
	protected $o = array();

	/**
	 * Construct 
	 * @param $cursor current directory
	 * @param $file current file in directory
	 * @param $next what to look for 
	 */
	function __construct($cursor, $file, $next, $parent = NULL, $iter = 0, $limit = 13)
	{
		if($limit <= 0)
			throw new Error('VRD: nesting limit reached');

		//set this thing
		$this->c = $cursor;
		$this->f = $file;
		$this->n = $next;
		$this->p = $parent;
		$this->l = $limit;
		$this->i = $iter;
	}

	///Do the messy job
	function go()
	{

	}

	/**
	 * Render subpath
	 * @param $path what to render
	 * @param $param what to pass
	 */
	function subnode($path, $param = array())
	{
		//new VRD($path, '', )
		//this->o[] = @up
		//@up->go()
	}

	/**
	 * Launch suggested new node
	 * @param $path where to go, unless not to go
	 * @param $param what to pass, unless not to pass
	 */
	function node($path, $param = array())
	{
		//if(!this->next)
		//  this->next = $path;
	}
}

/**
 * View/HTML class
 */
class View extends NoInst
{
	///Main template used
	const TEMPLATE = '/templ/index.php';
	///Everything that wil be added in <head>here</head>
	private static $HTMLhead = array();
	///Depth of working mode
	private static $depth = 13;

	/**
	 * DEPRECATED
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
	 * @param $depth render mode
	 */
	public static function r($path, array $params = array(), $depth = 1)
	{
		if(!View::viewExists($path))
			throw new ErrorHTTP('View "'.$path.'" does not exist', 404);

		switch ($depth)
		{
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
		if(self::lock())
			return;

		pre_dump($path);die;

		ob_start();
		if(is_int(self::$workmode) && self::$workmode >= 0)
			//FIXME: redo with VRD
			;// View::r($path[0], array(), self::$workmode);
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
		echo '<span id="exec-time">InEXt: '.round(((microtime(true) - NOW_MICRO)*1000.0), 3).'ms</span>';
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

	///Return site title, based on whatever needed
	public static function title()
	{
		return 'codename teo';
	}
}
