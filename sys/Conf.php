<?php //r3vCMS /sys/Conf.php
namespace CMS;

/**
 * Conf
 * Project configuration handler class
 */
class Conf /*extends \_Locks*/ {
	private static $conf = array();
	private static $db = array();

	public static function load() {
		$json = Mod::jsonFromFile('/appdata/config.json');

		if (!isset($json['r3v_config']))
			return;

		$json = $json['r3v_config'];

		if (isset($json['database']))
			self::$db = $json['database'];
		unset($json['database']);

		self::$conf = $json;
	}

	public static function get($key) {
		throw new Error501();
	}

	///Get database connection config
	public static function db() {
		$trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
		if ($trace[1]['class'] != 'CMS\\DB\\Base' || $trace[1]['function'] != 'go')
			return false;

		$data = self::$db;

		self::$db = array();
		return $data;
	}
}

Conf::load();
