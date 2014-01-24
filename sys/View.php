<?php ///r3vCMS /sys/View.php
namespace CMS;

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
	///Cursor
	protected $cursor = '';
	///Next nodes
	protected $next = array();
	///Local parameters to pass
	protected $vars = array();

	/**
	 * Construct
	 * @param $cursor current node
	 * @param $next nodes stack
	 * @param $parent NULL|ViewGen
	 * @param $param CMS params override
	 * @param $findindex look for index.php in $cursor?
	 */
	function __construct($cursor, array $next = array(), $parent = NULL, $findindex = TRUE) {
		if (((static::$i--)) <= 0)
			throw new Error('ViewGen: object count limit reached');
		if (!is_bool($findindex))
			throw new Error('ViewGen: $findindex is not bool');

		//set this thing
		$this->cursor = $cursor;
		if (!$this->checkPath())
			throw new \Error404('Node not found: '.$cursor);
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
		$this->trace[] = $node;
		if($was_index !== NULL)
			$this->find_index = !$was_index;
	}

	///Is $this->cursor valid?
	protected function checkPath() {
		return CMS::dirExists($c = '/view/'.$this->cursor) || CMS::fileExists($c.'.php');
	}

	///Is $this->next empty?
	protected function checkNext() {
		return !$this->next || (isset($this->next[0]) && !$this->next[0]);
	}

	/**
	 * Render subpath
	 * @param $path what to render
	 * @param $vars what to pass
	 */
	function subnode($path, array $vars = array()) {
		return (new ViewGen($path, array(), $this))->node(NULL, $vars);
	}

	/**
	 * MAIN FUNCTION
	 * Launch suggested current node or proceed this way with $this params
	 * @param $path where to go, unless we already know the path
	 * @param $vars additional parameters to pass
	 */
	function node($path = NULL, array $vars = array()) {
		if (is_string($path) && $path) {
			if ($path[0] == '/')
				return $this->subnode($path, $vars);

			//if we don't know where to go...
			if ($this->checkNext())
				$this->next = (array) explode('/', $path);
		} elseif ($path === '' && $this->checkNext())
			return;
		$this->vars = $vars;
		unset($vars, $path);

		$this->log($this->cursor);

		do {
			//check current dir, then proceed
			if (CMS::dirExists($dir = '/view'.$this->cursor)) {
				if($this->find_index && CMS::fileExists($file = $dir.'/index.php')) {
					//index.php
					$this->log('index.php', TRUE);
					$this->inc($file);
					if(!$this->next)
						return;
				} elseif (((($this->next))) && ($next = array_shift($this->next))) {
					//NEXT/ (proceed to directory)
					$this->log($next, FALSE);
					$this->cursor .= '/'.$next;
					continue;
				} else
					return;
			} elseif (CMS::fileExists($file = $dir.'.php')) {
				//cursor is a file
				$this->log($file, FALSE);
				$this->inc($file);
				if(!$this->next)
					return;
			}

			throw new \Error404();
		} while(TRUE);
	}

	///Redirect through HTTP headers to a $path
	public function redirect($path) {
		throw new \Redirect($path);
	}

	///Scope sandbox
	private function inc($file) {
		foreach ($this->vars as $k => $v)
			${$k} = $v;
		$this->vars = array();
		include ROOT.$file;
	}

	///check if user has authentication of $auth
	function guard_auth($auth) {
		throw new \Error501();
	}

	///Allow viewing only by user
	function guard_user() {
		if (!($m = Me::id()))
			throw new \Error403();
		return $m;
	}

	function guard_nonuser($redir = '/') {
		if (Me::id())
			$this->redirect($redir);
	}

	///Guard: node is available only as part of another view when doing FULL render
	function guard_nonrequest() {
		if ($this->parent === NULL && !AJAX)
			throw new \Error400('Direct node render disallowed');
	}
}

/**
 * View/HTML class
 */
class View extends \_Locks {
	///Main template used
	const TEMPLATE = '/appdata/index.php';
	///Everything that wil be added in <head>here</head>
	private static $HTMLhead = array();
	///Generated body
	private static $BODY = '';
	///Title of page
	private static $TITLE = '[teO]';
	///Additional title parts
	private static $TITLE_ADD = array();

	/**
	 * Try to retrieve and render view, handle errors
	 * @param $path path from CMS
	 */
	public static function go() {
		if (self::lock())
			return;

		CMS::setContentType('html');

		$path = Vars::URI(array('r3v/path', 'r3v/nodes'));

		if (AJAX) {
			$next = array();
			$path = $path['r3v/path'];
		} else {
			$next = $path['r3v/nodes'];
			$path = '';
		}

		ob_start();

		try {
			(new ViewGen($path, $next))->node();
		} catch (\ErrorHTTP $e) {
			ob_end_clean();
			ob_start();
			echo '<div class="clear"></div><div class="errorhttp center">', $e->getFancyMessage();
			if (DEBUG)
				echo '<br><br><div style="font-size:0.9em;text-align:left;margin:0 auto;width:70%;">', Error::prettyTrace($e->getTrace()), '</div>';
			echo '</div>';
		}

		self::$BODY = ob_get_clean();

		CMS::flushHeaders();
		if (AJAX)
			self::body();
		elseif (!(CMS::safeIncludeOnce(self::TEMPLATE)))
			throw new Error('View: No template found');
	}

	///Render footer
	public static function footer() {
		echo '<span id="exec-time">', round(((microtime(true)*10000) - NOW_MICRO)/10, 2), 'ms</span>';
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
				throw new Error('Parameter is not a string!');
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
		return self::$TITLE . ' '. implode(self::$TITLE_ADD);
	}

	/**
	 * Add a string to title or flush the buffer
	 * @param $str
	 * @return if $str=NULL: array
	 */
	public static function titleAdd($str = NULL) {
		if ($str === NULL) {
			$r = self::$TITLE_ADD;
			//self::$TITLE_ADD = array();
			return $r;
		} else
			self::$TITLE_ADD[] = $str;		
	}

	///Echo rendered body
	public static function body() {
		echo self::$BODY;
	}
}
