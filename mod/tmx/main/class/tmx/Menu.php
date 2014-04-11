<?php ///theMaxx site \tmx\Menu
namespace tmx;

/**
 * Menu generator
 */
class Menu {
	protected static $left = [];
	protected static $right = [];

	protected static function format(array $a, array $params = []) {
		foreach ($a as &$v) {
			if (!is_array($v))
				$v = ['name' => $v];
			if ($params)
				$v = array_merge_recursive($v, $params);
		}
		unset($v);
		reset($a);
		return $a;
	}

	public static function append(array $a) {
		self::$left += self::format($a);
	}

	public static function prepend(array $a) {
		self::$left = self::format($a) + self::$left;
	}

	public static function addright(array $a) {
		self::$right += self::format($a, ['class' => ['right']]);
	}

	public static function getLeft() {
		return self::$left;
	}

	public static function getRight() {
		return self::$right;
	}
}
