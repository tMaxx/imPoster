<?php
namespace CMS;
/**
 * Modloader class
 */
class Mod {
	private static $sysclass = array();
	private static $class = array();

	private static $service = array();
	private static $view = array();

	/**
	 * Returns an array retrieved from json file from path
	 * @param $path relative to /
	 * @return false|array
	 */
	public static function jsonFromFile($path) {
		if (($path = file_get_contents(ROOT.$path)) === false)
			return false;
		return json_decode($path,true);
	}

	///Set system files definition
	public static function go() {
		$sys = self::jsonFromFile('/sys/_def.json');
		if (!$sys || !isset($sys['r3v']['class']))
			throw new Error('System classes definition not found');

		self::$sysclass = $sys['r3v']['class'];
	}

	/**
	 * Class freeloader
	 * @param $class class name
	 */
	public static function class_load($class) {
		if (isset(self::$sysclass[$class]))
			require_once ROOT.'/sys/'.self::$sysclass[$class];
		elseif (file_exists(ROOT.'/app/'.$class.'.php'))
			require_once ROOT.'/app/'.$class.'.php';
		elseif (isset(self::$classes[$class]))
			require_once ROOT.self::$classes[$class];
		else
			throw new \ErrorCMS('Class not found: '.$class);
	}

	/**
	 * Load definition of a mod
	 * @param $name of mod
	 * @return bool status
	 */
	public static function loadDef($name) {
		if (!is_dir($name = '/mod/'.$name.'/'))
			return false;
		if (!($def = self::jsonFromFile($name.'def.json')))
			return false;
		if (!isset($def['r3v']))
			return false;

		$def = $def['r3v'];

		if (isset($def['class']))
			self::registerClasses($def['class'], $name);
		if (isset($def['service']))
			self::registerServices($def['service'], $name);
		if (isset($def['view']))
			self::registerViews($def['view'], $name);

		return true;
	}

	/**
	 * Insert data into self::{arr}, w. value prefix
	 * @param $arr name of array
	 * @param $data what to insert
	 * @param $prefix optional prefix
	 */
	protected static function insert($arr, $data, $prefix = '') {
		foreach ($data as $k => $v)
			//don't overwrite; don't write if file is not present
			if (!isset(self::${$arr}[$k]) && is_file($v = $prefix.$v))
				self::${$arr}[$k] = $v;
	}

	public static function registerClasses(array $in, $src) {
		self::insert('class', $in, $src);
	}

	public static function registerServices(array $in, $src) {
		self::insert('service', $in, $src);
	}

	public static function registerViews(array $in, $src) {
		self::insert('view', $in, $src);
	}

	public static function checkServices() {
		return false;
		// if (service exists from cms uri var) {
		//	load and launch service;
		//	return true;
		// }
	}
}
Mod::go();
