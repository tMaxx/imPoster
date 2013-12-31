<?php
namespace CMS;
/**
 * Modloader class
 */
class Mod extends \_Locks {
	private static $system = array();
	private static $classes = array();

	public static function go() {
		if (self::lock())
			return;

		self::$system = array(
			'CMS' => 'CMS.php',
			'Form' => 'Form.php',
			'CMS\\Session' => 'Session.php',
			'CMS\\View' => 'View.php',
			'CMS\\DB\\Base' => 'DB.php',
		);
	}

	/**
	 * Class freeloader
	 * @param $class class name
	 */
	public static function class_load($class) {
		$classp = $class . '.php';

		if (isset(self::$system[$class]))
			require_once ROOT.'/sys/'.self::$system[$class];
		// elseif (isset(self::$classes[$class]))
		// 	require_once ROOT. ...;
		elseif (file_exists(ROOT.'/app/'.$classp))
			require_once ROOT.'/app/'.$classp;
		else
			throw new \ErrorCMS('Class not found: '.$class);
	}

	public static function load($modname) {
		// code...
	}

	public static function registerClass(array $in = array()) {
		// code...
	}
}
Mod::go();
