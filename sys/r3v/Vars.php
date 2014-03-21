<?php ///r3vCMS \r3v\Vars
namespace r3v;

/**
 * Vars - variables handling class
 */
class Vars extends \_Locks {
	protected static $get = array();
	protected static $post = array();
	protected static $cookie = array();
	protected static $server = array();
	protected static $uri = array();

	///"Hack" to initialize class
	public function __construct() {
		if (self::lock())
			return;

		//revamp request to something more readable
		$ipath = isset($_GET['__req__']) ? ((array) explode('/', $_GET['__req__'])) : [];
		unset($_GET['__req__']);

		$rpath = '/';
		self::$uri['r3v/nodes'] = array();
		foreach ($ipath as $k => $v) {
			if (empty($v) || !is_numeric($k))
				continue;
			$e = (array) explode(':', $v, 2);
			$rpath .= $e[0];
			self::$uri['r3v/nodes'][] = $e[0];
			if (isset($e[1]))
				self::$uri[$e[0]] = $e[1];
			$rpath .= '/';
		}
		self::$uri['r3v/path'] = substr($rpath, 0, -1);

		self::$get = $_GET;
		self::$post = $_POST;
		self::$server = $_SERVER;
		self::$cookie = $_COOKIE;
		unset($_GET, $_POST, $_REQUEST, $_SERVER, $_COOKIE);
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
	 * @throws \r3v\Error
	 * @return array|input
	 */
	public static function __callStatic($name, $args) {
		$name = strtolower($name);
		if (!property_exists(self::class, $name))
			throw new Error("Property $name does not exist");

		if (!$args)
			return array_copy(self::${$name});

		$a = self::${$name};
		$in = $args[0];
		if ($not_array = !is_array($in))
			$in = (array) $in;
		$unset = isset($args[1]) ? !!$args[1] : false;

		$r = array();
		foreach ($in as $k => $v) {
			$has_default = !!is_string($k);
			$k = $has_default ? $k : $v;
			if (array_key_exists($k, $a)) {
				$r[$k] = $a[$k];
				if ($unset)
					unset(self::${$name}[$k]);
			} elseif ($has_default)
				$r[$k] = $in[$k];
		}

		if ($not_array) {
			if ($r)
				return $r[$in[0]];
			return NULL;
		}
		return $r;
	}
}

new Vars();
