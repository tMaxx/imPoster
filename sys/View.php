<?php ///revCMS /sys/View.php
///Actually just a complete view generator
class ViewGen {
	///Mode
	protected $find_index = TRUE;
	///Iterator - num of objects made
	protected static $i = 20;
	///$this' parent object
	protected $parent = NULL;
	//Called pages stack
	protected $trace = array();
		///Last node from stack
		protected $tracel = '';
	///Cursor
	protected $cursor = '';
	///Next nodes
	protected $next = array();

	/**
	 * Construct
	 * @param $cursor current node
	 * @param $next nodes stack
	 * @param $parent NULL|ViewGen
	 * @param $param CMS params override
	 * @param $findindex look for index.php in $cursor?
	 */
	function __construct($cursor, array $next, $parent = NULL, array $param = array(), $findindex = TRUE) {
		if (((static::$i--)) <= 0)
			throw new Error('ViewGen: object count limit reached');
		if (!is_bool($findindex))
			throw new Error('ViewGen: $findindex is not bool');

		//set this thing
		$this->cursor = $cursor;
		$this->find_index = $findindex;
		$this->next = $next;
		$this->parent = $parent;
	}

	/**
	 * Push $node on stack trace
	 * @param $node index.php, NEXT.php, NEXT/
	 * @param $was_index
	 */
	protected function log($node, $was_index = NULL) {
		$this->trace[] = $this->tracel = $node;
		if($was_index !== NULL)
			$this->find_index = !$was_index;
	}

	/**
	 * Render subpath
	 * @param $path what to render
	 * @param $param what to pass
	 */
	function subnode($path, $param = array()) {
		return (new ViewGen($path, array(), $this))->node();
	}

	/**
	 * MAIN FUNCTION
	 * Launch suggested current node or proceed this way with $this params
	 * @param $path where to go, unless we already know the path
	 * @param $param -- '' --
	 */
	function node($path = NULL, array $param = array()) {
		if (is_string($path)) {
			if (!$path)
				throw new Error404('ViewGen: No path provided');
			elseif ($path[0] == '/')
				return $this->subnode($path, $param);

			//if we don't know where to go...
			if (!$this->next || (isset($this->next[0]) && !$this->next[0])) {
				$this->next = (array) explode('/', $path);
				$this->param = $param;
				unset($param);
			}
		}

		$this->log($this->cursor);

		while (TRUE) {
			//check current dir, then proceed
			if (CMS::fileExists($dir = '/view'.$this->cursor)) {
				if($this->find_index && CMS::fileExists($file = $dir.'/index.php')) {
					//index.php
					$this->log('index.php', TRUE);
					return (include ROOT.$file);
				} elseif ($this->next && $next = array_shift($this->next)) {
					if (CMS::fileExists($file = $dir.'/'.$next.'.php')) {
						//NEXT.php (file)
						$this->log($next.'.php', FALSE);
						return (include ROOT.$file);
					} elseif (CMS::fileExists($dir = $dir.'/'.$next)) {
						//NEXT/ (directory)
						$this->log($next, FALSE);
						$this->cursor = $dir;

						continue;
					}
				} else
					return TRUE;
			} elseif (CMS::fileExists($file = $dir.'.php')) {
				//cursor is a file
				$this->log($file, FALSE);
				return (include ROOT.$file);
			}
			throw new Error404();
		}
	}

	function guard_auth($guard, $defpath) {
		// if (!)
	}

	///Guard: node is available only as part of another view when doing FULL render
	function guard_nonrequest() {
		if ($this->parent === NULL && !View::isMode('FULL'))
			throw new Error400('ViewGen: Node render disallowed');
	}

	//
	function guard_mode() {
		// if (View::isMode(func_get_args()));
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
	///Title of page
	private static $TITLE = 'Codename teo';

	/**
	 * Try to retrieve and render view, handle errors
	 * @param $path path from CMS
	 */
	public static function go($path) {
		if (self::lock())
			return;

		if (self::isMode('FULL', 'PART', 'SINGLE')) {
			$cursor = '/';
			$next = array_shift($path[1]);
			if (MODE == 'SINGLE') {
				$cursor = $path[0];
				$next = array();
			}

			ob_start();

			(new ViewGen($cursor, $path[1]))->node();

			self::$BODY = ob_get_clean();

			if (self::isMode('FULL')) {
				if (!(CMS::safeIncludeOnce(self::TEMPLATE)))
					throw new Error('View: No template found');
			} else {
				CMS::headers();
				self::body();
			}
		}
		else
			throw new ErrorHTTP('View: Unsupported working mode "'.MODE.'"!', 400);
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
		return self::$TITLE;
	}

	public static function body() {
		echo self::$BODY;
	}
}
