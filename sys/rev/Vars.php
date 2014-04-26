<?php ///rev engine \rev\Vars
namespace rev;

/**
 * Vars - variables handling class
 */
class Vars {
	protected static $URI = array();

	///"Hack" to initialize class
	public function __construct() {
		//revamp request to something more readable
		$ipath = (array) explode('/', REQUEST_URI);

		//filter out any html-ish characters
		$repl = function ($v) {
			return str_replace(['<', '>', '&', '?', '#'], 0xFFFD, $v);
		};

		$rpath = '/';
		self::$URI['rev/nodes'] = array();
		foreach ($ipath as $k => $v) {
			if (empty($v) || !is_numeric($k))
				continue;
			$e = (array) explode(':', $repl($v), 2);
			$rpath .= $e[0];
			self::$URI['rev/nodes'][] = $e[0];
			if (isset($e[1]))
				self::$URI[$e[0]] = $e[1];
			$rpath .= '/';
		}
		self::$URI['rev/path'] = $repl(substr($rpath, 0, -1));
	}

	/**
	 * Get defined vars from class variable
	 * @param $name of the variable, case-insensitive
	 * @param $args arguments
	 * @example If specified var_name can't be found in self::${$name} then...:
	 *    array("var_name" => if_not_set,)
	 *       value of if_not_set will be appended to result
	 *    array("var_name")
	 *       nothing will be appended to result
	 * @throws \rev\Error
	 * @return array|input
	 */
	public static function __callStatic($name, $args) {
		$name = strtoupper($name);
		if (!property_exists(__CLASS__, $name)) {
			if (!isset($GLOBALS['_'.$name]))
				throw new Error("Property $name does not exist");
			$a = &$GLOBALS['_'.$name];
			$name = false; //is self
		} else
			$a = &self::${$name};

		if (!$args)
			return $a;

		$unset = isset($args[1]) ? $args[1] === true : false;
		$in = $args[0];
		if (!is_array($in)) {
			if (array_key_exists($in, $a)) {
				$in = &$a[$in];
				if ($unset)
					unset($a[$in]);
				return is_array($in) ? array_copy($in) : $in;
			}
			return null;
		}

		$r = array();
		foreach ($in as $k => $v) {
			$has_default = !!is_string($k);
			$k = $has_default ? $k : $v;
			if (array_key_exists($k, $a)) {
				$r[$k] = $a[$k];
				if ($unset)
					unset(self::${$name}[$k]);
			} elseif ($has_default)
				$r[$k] = &$in[$k];
		}

		return array_copy($r);
	}
}

new Vars();
