<?php ///rev engine \r3v\View
namespace r3v;

/**
 * View/HTML class
 */
class View {
	/** Everything that wil be added in <head>here</head> */
	protected static $HTML_head = [];
	/** Additional title part */
	protected static $HTML_title = '';

	/** HTTP headers */
	protected static $HTTP_headers = [];

	/** Route paths config */
	protected static $config = [];

	/** Current/selected route path dir */
	protected static $basepath = [];

	/** Render view, handle thrown HTTP errors */
	public static function go() {
		if (self::$config)
			return;

		self::setContentType('html');
		self::$config = Conf::get('site');
		$node = Vars::uri('r3v/nodes');
		$routes = Mod::getRoutePaths();

		$selected = isset($node[0], $routes[$node[0]]) ? $node[0] : '';
		$len = strlen($selected) + 1;
		$selected = $routes[$selected];
		self::$basepath = $selected['dir'];
		unset($routes);

		$no_template = empty($selected['template']);
		$reqpath = substr(Vars::uri('r3v/path'), $len);

		ob_start();
		try {
			Mod::runFuncArray($selected['autorun']);

			if ((!$selected['force_path']) && $reqpath && (strncmp($reqpath, 'index', 5) != 0))
				$subview = new View\Explicit($selected['dir'], $reqpath);

			(new View\Explicit(
				$selected['dir'],
				($selected['force_path'] ?: ''),
				['_view_child' => (isset($subview) ? $subview : null)]
			))->go();

		} catch (ErrorHTTP $e) {
			ob_clean();

			self::$HTML_title = '';
			self::$HTML_head = [];

			if ($selected['error_page'])
				(new View\Explicit($selected['dir'], $selected['error_page'], ['__error' => $e]))->go();
			else {
				self::$HTML_title = 'Oopsie no'.$e->httpcode;
				if ($no_template)
					echo '<html><head><title>', self::title(), '</title></head><body>';
				echo '<div><h1>HTTP ', $e->httpcode, '</h1>', $e->inmessage;
				if (DEBUG)
					echo '<div>', Error::formatErrorLine($e->getFile(), $e->getLine()),
						'<br>', Error::prettyTrace($e->getTrace()), '</div>';
				echo '</div>';
				if ($no_template)
					echo '<br><br>', ms_from_start(), '</body></html>';
			}
		}

		self::flushHTTPheaders();
		if (AJAX || $no_template)
			ob_end_flush();
		else {
			$BODY = ob_get_clean();
			include ROOT.$selected['template'];
		}
	}

	/**
	 * Add new header to be set later
	 * @param $html
	 */
	public static function addToHead($html) {
		foreach ((array)$html as $v) {
			if (!is_string($v))
				throw new Error('Parameter is not a string!');
			self::$HTML_head[] = $v;
		}
	}

	/**
	 * Add a string to title
	 * @param $str
	 */
	public static function addToTitle($str) {
		self::$HTML_title .= (string) $str;
	}

	/** Return title, imploded */
	protected static function title() {
		return self::$config['title'].(self::$HTML_title ? ' | '.self::$HTML_title : '');
	}

	/**
	 * Add HTTP header for later flushing
	 * @param $header
	 */
	public static function addHTTPheaders($header) {
		foreach ((array)$header as $k => $v) {
			if (!is_string($v))
				throw new Exception('Parameter is not a string!');
			if (is_numeric($k))
				self::$HTTP_headers[] = $v;
			else
				self::$HTTP_headers[$k] = $v;
		}
	}

	/**
	 * Set content-type header
	 * @param $type
	 * @return bool success
	 */
	public static function setContentType($type) {
		switch (strtolower($type)) {
			case 'js':
				$type = 'javascript';
			case 'pdf':
			case 'json':
			case 'javascript':
				$type = 'application/'.$type;
				break;
			case 'text':
				$type = 'text/plain';
				break;
			case 'plain':
			case 'html':
			case 'css':
				$type = 'text/'.$type;
				break;
			case 'jpeg':
			case 'gif':
			case 'png':
				$type = 'image/'.$type;
				break;
			default:
				return false;
				break;
		}
		self::$HTTP_headers['content-type'] = 'Content-Type: '.$type.';charset=UTF-8';
		return true;
	}

	///Flush headers
	public static function flushHTTPheaders() {
		foreach (self::$HTTP_headers as $v)
			header($v, true);
		self::$HTTP_headers = [];
	}

	/** Redirect to given $path */
	public static function redirect($path) {
		self::addHTTPheaders('Location: '.filter_var($path, FILTER_SANITIZE_URL));
		self::flushHTTPheaders();
		die;
	}

	/** Return current basepath */
	public static function getCurrentBasepath() {
		return self::$basepath;
	}
}

if (!CLI)
	View::addHTTPheaders([
		'Server: got hella wasted',
		'X-Powered-By: lots of self-esteem',
		'X-Backend: '.r3v_ID,
	]);

Mod::registerUnload('r3v\\View', ['\\r3v\\View::flushHTTPheaders']);
