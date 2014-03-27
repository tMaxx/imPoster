<?php ///r3vCMS /sys/r3v/View.php
namespace r3v;

/**
 * View/HTML class
 */
class View extends \_Locks {
	/** Everything that wil be added in <head>here</head> */
	private static $HTML_head = array();
	/** Additional title parts */
	private static $HTML_title = array();

	/** Route paths config */
	protected static $config = [];

	/**
	 * Try to retrieve and render view, handle errors
	 * @param $path path from CMS
	 */
	public static function go() {
		if (self::lock())
			return;

		self::$config = Conf::get('site');
		$node = Vars::uri('r3v/nodes');
		$routes = Mod::getRoutePaths();

		$selected = isset($node[0], $routes[$node[0]]) ? $node[0] : '';
		$len = strlen($selected) + 1;
		$selected = $routes[$selected];
		unset($routes);

		$no_template = empty($selected['template']);
		$reqpath = substr(Vars::uri('r3v/path'), $len);

		ob_start();
		try {
			if (($reqpath && strncmp($reqpath, 'index', 5) != 0) && !$selected['force_path'])
				$cont = new View\Explicit($selected['dir'], $reqpath);

			(new View\Explicit(
				$selected['dir'],
				($selected['force_path'] ?: ''),
				['__view_child' => (isset($cont) ? $cont : false)]
			))->go();

		} catch (\ErrorHTTP $e) {
			ob_clean();

			self::$HTML_title = ['Oopsie no', $e->httpcode];
			self::$HTML_head = [];

			if ($selected['error_page'])
				(new View\Explicit($selected['dir'], $selected['error_page'], ['__error' => $e]))->go();
			else {
				if ($no_template)
					echo '<html><head><title>', self::title(), '</title></head><body>';
				echo '<div><h1>HTTP ', $e->httpcode, '</h1>', $e->inmessage;
				if (DEBUG)
					echo '<div><i>@</i>', Error::pathdiff($e->getFile()), ':', $e->getLine(),
						'<br>', Error::prettyTrace($e->getTrace()), '</div>';
				echo '</div>';
				if ($no_template)
					echo '<br><br>', ms_from_start(), '</body></html>';
			}
		}

		HTTP::setContentType('html');
		HTTP::flush();
		if (AJAX || $no_template)
			ob_end_flush();
		else
			(new View\Template($selected['template']))->replace([
				'BODY' => ob_get_clean(),
				'HEAD' => implode(self::$HTML_head),
				'TITLE' => self::title(),
				'EXECTIME' => ms_from_start()
			])->echoo();
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
	public static function titleAdd($str = NULL) {
		self::$HTML_title .= (string) $str;
	}

	/** Return title, imploded */
	protected static function title() {
		return self::$config['title'].(!self::$HTML_title ?: ' | '.self::$HTML_title);
	}
}
