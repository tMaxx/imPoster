<?php ///r3vCMS /sys/r3v/View.php
namespace r3v;

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

		HTTP::setContentType('html');

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
			(new View\Gen($path, $next))->node();
		} catch (\ErrorHTTP $e) {
			ob_end_clean();
			ob_start();
			echo '<div class="clear"></div><div class="errorhttp center">', $e->getFancyMessage();
			if (DEBUG)
				echo '<br><br><div style="font-size:0.9em;text-align:left;margin:0 auto;width:70%;">', Error::prettyTrace($e->getTrace()), '</div>';
			echo '</div>';
		}

		self::$BODY = ob_get_clean();

		HTTP::flushHeaders();
		if (AJAX)
			self::body();
		elseif (!(File::safeIncludeOnce(self::TEMPLATE)))
			throw new Error('View: No template found');
	}

	///Render footer
	public static function footer() {
		echo '<span id="exec-time">', ms_from_start(), 'ms</span>';
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
