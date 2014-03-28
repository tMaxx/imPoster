<?php //r3v engine /sys/Conf.php
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

		if (isset($json['database']))
			self::$db = $json['database'];
		unset($json['database']);

		self::$conf = $json;
	}

	/**
	 * Get key from project config
	 * @param $key where '/' is a separator for nested array keys
	 * @param &$warn
	 */
	public static function get($key, &$warn = null) {
		if (!is_string($key))
			throw new Error('Invalid key: not a string');

		$key = explode('/', $key);
		$current = self::$conf;
		while (($e = array_shift($key)) !== null) {
			if (!isset($current[$e])) {
				$warn = true;
				throw new Error("Invalid key request: $e");
			}
			$current = $current[$e];
		}

		return $current;
	}

	///Get database connection config
	public static function db() {
		$trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
		if ($trace[1]['class'] != 'r3v\\DB\\Base' || $trace[1]['function'] != 'go')
			return false;

		if (self::$db['dev_hostname'] == gethostname()) //dev
			$data = self::$db['dev'];
		else
			$data = self::$db['stable'];

		self::$db = [];
		return $data;
	}
}

Conf::load();
