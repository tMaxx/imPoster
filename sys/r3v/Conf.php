<?php //r3v engine \r3v\Conf
namespace r3v;

/**
 * Conf
 * Project configuration handler class
 */
class Conf {
	private static $conf = [];
	private static $db = null;
	protected static $env_type = null;

	/** Load project config */
	public static function load() {
		$type = glob(ROOT.'/r3v_env.*');
		if (!isset($type[0]))
			throw new Error('Project environment not set!');

		self::$env_type = substr($type[0], strlen(ROOT)+9);

		$json = Mod::readJsonFromFile('/config.json');
		if (isset($json['__default']))
			$json = array_replace_recursive($json['__default'], $json[self::$env_type]);
		else
			$json = $json[self::$env_type];

		if (!defined('DEBUG'))
			define('DEBUG', !!$json['debug']);

		if (isset($json['database'])) {
			self::$db = $json['database'];
			unset($json['database']);
		}

		self::$conf = $json;
	}

	/**
	 * Get key from project config
	 * @param $key where '/' is a separator for nested array keys
	 * @param $dnt Do Not Throw (return null)
	 * @return key/null
	 */
	public static function get($key, $dnt = false) {
		if (!is_string($key))
			throw new Error('Invalid key: not a string');

		$key = explode('/', $key);
		$current = self::$conf;
		while (($e = array_shift($key)) !== null) {
			if (!isset($current[$e])) {
				if ($dnt)
					return null;
				throw new Error("Invalid key request: $e");
			}
			$current = $current[$e];
		}

		return $current;
	}

	/** Get database connection config */
	public static function db() {
		if (!self::$db)
			throw new Error('No DB config available');
		$data = self::$db;
		self::$db = null;
		return $data;
	}

	/**
	 * Get environment type
	 * @return string
	 */
	public static function envType() {
		return self::$env_type;
	}
}

Conf::load();
