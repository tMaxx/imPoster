<?php ///r3v engine \r3v\File
namespace r3v;

/**
 * File - management class
 */
class File {
	/**
	 * Includes file in param
	 * @param $______file path to file, relative to /
	 * @return bool true on success
	 */
	public static function inc($______file) {
		if (!self::fileExists($______file))
			return false;
		include ROOT.$______file;
		return true;
	}

	/**
	 * Includes file in param
	 * @param $______file path to file, relative to /
	 * @return bool true on success
	 */
	public static function inc1($______file) {
		if (!self::fileExists($______file))
			return false;
		include_once ROOT.$______file;
		return true;
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
	 * Return scandir() without dots
	 * @param $dir ectory
	 * @param $exclude additional elements
	 * @return array
	 */
	public static function scandir($dir, $exclude = []) {
		return array_diff(scandir(ROOT.$dir), (['.', '..'] + $exclude));
	}

	/**
	 * Return path with any dots, slashes and tildes removed
	 * @param $path
	 * @return string
	 */
	public static function sanitizePath($path) {
		return str_replace(['..', '/', '~'], '', $path);
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

	/**
	 * Get contents of file w. base path
	 * @param $path to file
	 * @return false|string
	 */
	public static function contents($path) {
		if (!self::fileExists($path))
			return false;
		return file_get_contents(ROOT.$path);
	}
}
