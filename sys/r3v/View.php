<?php ///r3vCMS /sys/r3v/View.php
namespace r3v;

/**
 * View/HTML class
 */
class View extends \_Locks {
	///Everything that wil be added in <head>here</head>
	private static $HTMLhead = array();
	///Additional title parts
	private static $TITLE_ADD = array();

	protected static $path = null;
	protected static $node = null;
	protected static $config = [];

	/**
	 * Try to retrieve and render view, handle errors
	 * @param $path path from CMS
	 */
	public static function go() {
		if (self::lock())
			return;

		HTTP::setContentType('html');

		self::$path = Vars::uri('r3v/path');
		self::$node = Vars::uri('r3v/nodes');
		self::$config = Conf::get('site');
		$routes = Mod::getRoutePaths();

		$selected = $routes['/'];
		unset($routes['/']);
		$len = 1;

		foreach ($routes as $r => $m)
			if (strncmp($r, self::$path, $l = strlen($r)) == 0) {
				//this is it
				$selected = $m;
				$len = $l;
			}

		if ($selected['force_path'])
			$reqpath = $selected['force_path'];
		else
			$reqpath = substr(self::$path, $len);

		ob_start();
		try {
			//(new View\Recursive(AJAX ? self::$path : '', AJAX ? [] : self::$node))->node();
			$template = (new View\Explicit($selected['dir'], $reqpath))->go();
		} catch (\ErrorHTTP $e) {
			ob_end_clean();
			ob_start();
			echo '<div class="clear">',
				'</div><div class="errorhttp center">',
				'<h1 class="white">HTTP ', $e->httpcode, '</h1>',
				$e->inmessage;
			if (DEBUG)
				echo '<div class="trace"><i>@</i>',
					Error::pathdiff($e->getFile()), ':', $e->getLine(),
					'<br>',
					Error::prettyTrace($e->getTrace()),
					'</div>';
			echo '</div>';
		}
		$BODY = ob_get_clean();


		HTTP::flush();
		if (AJAX || empty($template))
			echo $BODY;
		else {
			$template = new View\Template($template);

			$template->replace([
				'BODY' => $BODY,
				'HEAD' => implode(self::$HTMLhead),
				'TITLE' => self::$config['title'].' '.implode(self::$TITLE_ADD),
				'FOOTER' => '<span id="exec-time">'.ms_from_start().'ms</span>'
			]);
			$template->echoo();
		}
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

	/**
	 * Add a string to title or flush the buffer
	 * @param $str
	 */
	public static function titleAdd($str = NULL) {
		self::$TITLE_ADD[] = (string) $str;
	}
}
