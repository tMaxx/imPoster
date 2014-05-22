<?php ///rev engine \rev\File
namespace rev;

/** File - management class */
class File {
	/**
	 * Includes file in param
	 * @param file path to file, relative to /
	 * @return bool true on success
	 */
	public static function inc() {
		if (!self::fileExists(func_get_arg(0)))
			return false;
		include ROOT.func_get_arg(0);
		return true;
	}

	/**
	 * Includes file in param
	 * @param file path to file, relative to /
	 * @return bool true on success
	 */
	public static function inc1() {
		if (!self::fileExists(func_get_arg(0)))
			return false;
		include_once ROOT.func_get_arg(0);
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
