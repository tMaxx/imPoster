<?php ///r3vCMS /sys/r3v/View.php
namespace r3v\View;
use \r3v\File;

///Actually just a complete view generator
class Gen {
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
	 * @param $parent NULL|View\Gen
	 * @param $param CMS params override
	 * @param $findindex look for index.php in $cursor?
	 */
	function __construct($cursor, array $next = array(), $parent = NULL, $findindex = TRUE) {
		if (((static::$i--)) <= 0)
			throw new \Error('View\\Gen: object count limit reached');
		if (!is_bool($findindex))
			throw new \Error('View\\Gen: $findindex is not bool');

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
		return File::dirExists($c = '/view/'.$this->cursor) || File::fileExists($c.'.php');
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
		return (new Gen($path, array(), $this))->node(NULL, $vars);
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
			if (File::dirExists($dir = '/view'.$this->cursor)) {
				if($this->find_index && File::fileExists($file = $dir.'/index.php')) {
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
			} elseif (File::fileExists($file = $dir.'.php')) {
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
