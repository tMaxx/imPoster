<?php ///revCMS /sys/View.php
///Actually just a complete view generator
class ViewRequiringDiscoverer
{
	///Cursor
	protected $cur = '';
	///Mode
	protected $mode = 0;
	///Next nodes
	protected $next = array();
	///Next include node
	protected $nn = NULL;
	///Iterator - num of objects made
	protected static $i = 40;
	///Upper (parent) object
	protected $par = NULL;
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
	 * 2: $next is file
	 * 3: $next is directory
	 */
	function __construct($cursor, $next, $parent = NULL, $mode = 0)
	{
		if(((static::$i--)) <= 0)
			throw new Error('VRD: object count limit reached');
		if(!is_int($mode))
			throw new Error('VRD: $mode is not an int');

		//set this thing
		$this->cur = $cursor;
		$this->mode = $mode;
		$this->next = $next;
		$this->par = $parent;
	}

	///Do the messy job
	function go()
	{
		if(CMS::fileExists($p = '/view'.$this->cur))
		{
			if($this->mode < 1 && CMS::fileExists($f = $p.'/index.php'))
			{
				$this->nn = $f;
				$this->mode = 1; //index file
			}
			elseif($nxt = array_shift($nxt_arr = $this->next))
			{
				if(CMS::fileExists($f = $p.'/'.$nxt.'.php'))
				{
					$this->nn = $f;
					$this->mode = 2; //file
				}
				elseif(CMS::fileExists($f = $p.'/'.$nxt))
				{
					$this->nn = $f;
					$this->mode = 3; //dir
					(new ViewRequiringDiscoverer($f, $nxt_arr, $this))->go();
					return;
				}
				else
					return;
			}
			else
				return;
		}
		elseif(CMS::fileExists($p .= '.php'))
		{
			$this->nn = $p;
			$this->mode = 2;
		}
		else
		{
			throw new ErrorHTTP('VRD: Node '.$this->cur.' does not exist!', 404);
			return;
		}

		$this->inc();
	}

	///Include files from $this->nn
	protected function inc()
	{
		include ROOT.$this->nn;
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
	 * Launch suggested current node
	 * @param $path where to go, unless not to go
	 * @param $param what to pass, unless not to pass
	 */
	function node($path, $param = array())
	{
		if($path && $path[0] == '/'){
			$this->subnode($path, $param);
			return;
		}

		$cur = $this->cur;
		$mode = 0;
		if($defn = (!$this->next || (isset($this->next[0]) && !$this->next[0])))
			$path = (array) explode('/', $path);
		else
			$path = $this->next;

		switch ($this->mode) {
			case 1:
				//current == index
				$mode = 2;
			case 2:
				//current == file
				$nxt = array_shift($path);
				$cur .= '/'.$nxt;
				break;
		}

		(new ViewRequiringDiscoverer($cur, $path, $this, $mode))->go();
	}

	function auth($guard, $defpath)
	{
		// if(!)
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
