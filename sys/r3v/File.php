<?php ///r3vCMS /sys/r3v/File.php
namespace r3v;

/**
 * File - management class
 */
class File {
	/**
	 * Includes file in param
	 * @param $file path to file, relative to /
	 * @return bool true on success
	 */
	public static function safeInclude($file) {
		if ($r = self::fileExists($file))
			include ROOT.$file;
		return $r;
	}

	/**
	 * Includes file in param
	 * @param $file path to file, relative to /
	 * @return bool true on success
	 */
	public static function safeIncludeOnce($file) {
		if ($r = self::fileExists($file))
			include_once ROOT.$file;
		return $r;
	}

	/**
	 * Check if file or directory exists on server
	 * @param $node path, relative to /
	 * @return bool
	 */
	public static function nodeExists($node) {
		return file_exists(ROOT.$file);
	}

	/**
	 * Check if $file exists in project
	 * @param $file path, relative to /
	 * @return bool
	 */
	public static function fileExists($file) {
		return is_file(ROOT.$file);
	}

	/**
	 * Check if $dir exists in project and is not a file
	 * @param $dir path, relative to /
	 * @return bool
	 */
	public static function dirExists($dir) {
		return is_dir(ROOT.$dir);
	}

	/**
	 * Check if class file exists
	 * @param $name class name
	 * @return bool
	 */
	public static function appClassExists($name) {
		return self::fileExists('/app/'.$name.'.php');
	}

	/**
	 * Return scandir() without dots
	 * @param $dir ectory
	 * @param $addit ional elements to exclude
	 * @return array
	 */
	public static function scandir($dir, $addit = []) {
		return array_diff(scandir(ROOT.$dir), (array('.', '..') + $addit));
	}

	/**
	 * Return path with any dots, slashes and tildes removed
	 * @param $path
	 * @return string
	 */
	public static function sanitizePath($path) {
		return str_replace(array('.', '/', '~'), '', $path);
	}

	/**
	 * Returns an array retrieved from json file from path
	 * @param $path relative to /
	 * @return false|array
	 */
	public static function jsonFromFile($path) {
		if (($path = file_get_contents(ROOT.$path)) === false)
			return false;
		return json_decode($path, true);
	}
}
