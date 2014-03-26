<?php //r3vCMS /sys/Conf.php
namespace r3v;

/**
 * Conf
 * Project configuration handler class
 */
class Conf {
	private static $conf = [];
	private static $db = [];

	public static function load() {
		$json = Common::jsonFromFile('/config.json');

		if (!isset($json['r3v_config']))
			return;

		$json = $json['r3v_config'];

		if (isset($json['database']))
			self::$db = $json['database'];
		unset($json['database']);

		self::$conf = $json;
	}

	/**
	 * Get key from project config
	 * @param $key
	 * 	/ - separator for nested array keys
	 * @param &$warn
	 */
	public static function get($key, &$warn = null) {
		if (!is_string($key))
			throw new Error('Invalid key: not a string');

		$key = explode('/', $key);
		$current = self::$conf;
		while (($e = array_shift($key)) !== null) {
			if (!isset($current[$e]))
				;
		}

		return $current;
	}

	///Get database connection config
	public static function db() {
		$trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
		if ($trace[1]['class'] != 'CMS\\DB\\Base' || $trace[1]['function'] != 'go')
			return false;

		$data = self::$db;

		self::$db = [];
		return $data;
	}
}

Conf::load();
