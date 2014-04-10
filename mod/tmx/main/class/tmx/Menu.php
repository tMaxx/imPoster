<?php ///theMaxx site \tmx\Menu

/**
 * Menu generator
 */
class Menu {
	protected static $left = [];
	protected static $right = [];

	public static function append(array $a) {
		self::$left += $a;
	}

	public static function prepend(array $a) {
		self::$left = $a + self::$left;
	}

	public static function addright(array $a) {
		self::$right += $a;
	}

	public static function getLeft() {
		return self::$left;
	}

	public static function getRight() {
		return self::$right;
	}
}
