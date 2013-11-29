<?php ///revCMS /sys/View.php
///Actually just a complete view generator
class ViewRequiringDiscoverer {
	///Mode
	protected $find_index = TRUE;
	///Next include node
	protected $nn = NULL;
	///Iterator - num of objects made
	protected static $i = 20;
	///$this' parent object
	protected $parent = NULL;
	//Called pages stack
	protected $ctx = array();
		///Last node from stack
		protected $ctxl = '';
	///Cursor
	protected $cur = '';
	///Next nodes
	protected $next = array();

	/**
	 * Construct
	 * @param $cursor current directory
	 * @param $next nodes stack
	 * @param $parent NULL|VRD
	 * @param $index look for index.php in $cursor?
	 */
	function __construct($cursor, $next, $parent = NULL, $index = TRUE) {
		if (((static::$i--)) <= 0)
			throw new Error('VRD: object count limit reached');
		if (!is_bool($index))
			throw new Error('VRD: $mode is not bool');

		//set this thing
		$this->cur = $cursor;
		$this->find_index = $index;
		$this->next = $next;
		$this->parent = $parent;
	}

	/**
	 * Push $value on stack
	 * @param $value
	 * @return $value
	 */
	protected function spush($value) {
		$this->ctx[] = $this->ctxl = $value;
		return $value;
	}

	///Do the messy job
	function go() {
		if (CMS::fileExists($p = '/view'.$this->cur)) {
			$nxt_arr = $this->next;
			if ($this->mode < 1 && CMS::fileExists($f = $p.'/index.php')) {
				$this->nn = $f;
				$this->mode = 1; //index file
			}
			elseif ($nxt = array_shift($nxt_arr)) {
				if (CMS::fileExists($f = $p.'/'.$nxt.'.php')) {
					$this->nn = $f;
					$this->mode = 2; //file
				}
				elseif (CMS::fileExists($f = $p.'/'.$nxt)) {
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
		elseif (CMS::fileExists($p .= '.php')) {
			$this->nn = $p;
			$this->mode = 2;
		}
		else {
			throw new ErrorHTTP('VRD: Node '.$this->cur.' does not exist!', 404);
			return;
		}

		$this->inc();
	}

	///Include files from $this->nn
	protected function inc() {
		include ROOT.$this->nn;
	}

	/**
	 * Render subpath
	 * @param $path what to render
	 * @param $param what to pass
	 */
	function subnode($path, $param = array()) {
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
	function node($path, $param = array()) {
		if (is_string($path) && $path[0] == '/') {
			$this->subnode($path, $param);
			return;
		}

		$cur = $this->cur;
		$mode = 0;
		if ($defn = (!$this->next || (isset($this->next[0]) && !$this->next[0])))
			$path = (array) explode('/', $path);
		else
			$path = $this->next;

		if ($this->mode == 1 || $this->mode == 2) {
			$nxt = array_shift($path);
			$cur .= '/'.$nxt;
		}

		(new ViewRequiringDiscoverer($cur, $path, $this, $this->mode))->go();
	}

	function guard_auth($guard, $defpath) {
		// if (!)
	}

	///Guard: node is available only as part of another view when doing FULL render
	function guard_nonrequest() {
		if ($this->parent === NULL && !View::isMode('FULL'))
			throw new ErrorHTTP('VRD: Node render disallowed', 400);
	}
}

/**
 * View/HTML class
 */
class View extends NoInst {
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
	public static function go($path) {
		if (self::lock())
			return;

		if (self::isMode('FULL', 'PART', 'SINGLE'))) {
			$cursor = '/';
			$next = array_shift($path[1]);
			if (MODE == 'SINGLE') {
				$cursor = $path[0];
				$next = array();
			}

			ob_start();

			(new ViewRequiringDiscoverer($cursor, $path[1]))->go();

			self::$BODY = ob_get_clean();

			if (self::isMode('FULL'))
				CMS::safeIncludeOnce('/templ/index.php');
			else {
				CMS::headers();
				self::body();
			}
		}
		else
			throw new ErrorHTTP('View: Unsupported working mode "MODE"!', 400);
	}

	/**
	 * Is MODE equal to one of parameters
	 * @param $mode strings
	 * @return bool
	 */
	public static function isMode() {
		return !!in_array(MODE, func_get_args());
	}

	///Render footer
	public static function footer() {
		echo '<span id="exec-time">InEXt: '.round(((microtime(true) - NOW_MICRO)*1000.0), 3).'ms</span>';
	}

	/**
	 * Add new header to be set later
	 * @param $html
	 */
	public static function addToHead($html) {
		if (!is_array($header))
			$header = array($header);

		foreach ($header as $v) {
			if (!is_string($v))
				throw new Exception('Parameter is not a string!');
			self::$HTMLhead[] = $v;
		}
	}

	///Render contents of $HTMLhead
	public static function head() {
		foreach (self::$HTMLhead as $v)
			echo $v;
	}

	///Return site title, based on whatever needed
	public static function title() {
		return 'codename teo';
	}

	public static function body() {
		echo self::$BODY;
	}
}
