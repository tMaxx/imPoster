<?php ///revCMS /sys/View.php
///Actually just a complete view generator
class ViewRequiringDiscoverer
{
	///Cursor
	protected $c = '';
	///Mode
	protected $m = 0;
	///Next nodes
	protected $n = array();
	///Iterator - num of objects made
	protected static $i = 40;
	///Upper (parent) object
	protected $p = NULL;
	//$this' created objects
	protected $o = array();

	/**
	 * Construct 
	 * @param $cursor current directory
	 * @param $next what to look for
	 * @param $parent VRD
	 * @param $mode finding mode
	 * 0: Free to choose
	 * 1: index.php in current $cursor path
	 * 2: Get $next
	 * 3: $next is file ($next)
	 * 4: $next is directory ($next)
	 */
	function __construct($cursor, $next, $parent = NULL, $mode = 0)
	{
		if(((static::$i--)) <= 0)
			throw new Error('VRD: object count limit reached');
		if(!is_int($mode))
			throw new Error('VRD: $mode is not an int');

		//set this thing
		$this->c = $cursor;
		$this->m = $mode;
		$this->n = $next;
		$this->p = $parent;
	}

	///Do the messy job
	function go()
	{
		pre_dump($this);
		$m = $this->m;
		if()

		return;
	}

	/**
	 * Render subpath
	 * @param $path what to render
	 * @param $param what to pass
	 */
	function subnode($path, $param = array())
	{
		$c = count($this->o);
		$this->o[] = new ViewRequiringDiscoverer($path, array(), $this);
		//don't copy object
		$r = $this->o[$c]->go();
		return $r;
	}

	/**
	 * Launch suggested new node
	 * @param $path where to go, unless not to go
	 * @param $param what to pass, unless not to pass
	 */
	function node($path, $param = array())
	{
		if(!$this->n)
			$this->n = $path;
		//$path may be not in current directory
		//i.e. user doesn't have access to some part of site
		$o = new ViewRequiringDiscoverer($this->cursor, array(), $this, 0);

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
	///Generated body, only if MODE is set as full
	private static $BODY = '';

	/**
	 * Try to retrieve and render view, handle errors
	 * @param $path path from CMS
	 */
	public static function go($path)
	{
		if(self::lock())
			return;

		if(in_array(MODE, array('FULL', 'PART', 'SINGLE')))
		{
			$cursor = '/';
			$next = array_shift($path[1]);
			if(MODE == 'SINGLE')
			{
				$cursor = $path[0];
				$next = array();
			}

			ob_start();

			(new ViewRequiringDiscoverer($cursor, $path[1]))->go();
			self::$BODY = ob_get_contents();

			ob_end_clean();

			if(MODE == 'FULL')
				CMS::safeIncludeOnce('/templ/index.php');
			else
			{
				CMS::headers();
				self::body();
			}
		}
		else
			throw new ErrorHTTP('View: Unsupported working mode "MODE"!', 400);
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

	public static function body()
	{
		echo self::$BODY;
	}
}
