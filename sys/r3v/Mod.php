<?php ///r3v engine \r3v\Mod
namespace r3v;

/**
 * Modloader class
 * Everything connected with loading and definitions
 */
class Mod {
	/** All mods definition */
	protected static $mods_def = [];
	protected static $mods_loaded = [];

	/** Class loading paths */
	protected static $class = [];

	/** Translate req_path->location */
	protected static $route = [];

	/** Loaded classes, w. optional autoloaders */
	protected static $loaded = [];


	/**
	 * Read json file from path
	 * @param $path
	 * @return false on failure, array otherwise
	 */
	public static function readJsonFromFile($path) {
		if (!is_file(ROOT.$path))
			return false;
		if (($def = file_get_contents(ROOT.$path)) === false)
			return false;
		if (!is_array($def = json_decode($def, true)))
			return null;

		return $def;
	}

	/**
	 * Run functions from array of arrays
	 * @param $arr ay of arrays
	 * @example parameter:
	 * [ ['function_to_run'], [[some_object, 'method'], 'arg1', 'argX'] ]
	 * First index in subarray *must* be callable or it will be ignored!
	 */
	public static function runFuncArray($arr) {
		foreach ((array)$arr as $a) {
			$a = (array) $a;
			$f = array_shift($a);
			if (is_callable($f))
				call_user_func_array($f, $a);
		}
	}

	/** Return modules assigned to paths */
	public static function getRoutePaths() {
		return self::$route;
	}

	/**
	 * Parse autoloader section
	 * @param $al autoloader
	 * @param $path, base
	 */
	protected static function parseAutoloader(array $al, $path) {
		if (isset($al['files'])) {
			foreach ((array) $al['files'] as $run)
				if (is_file(ROOT.$path.$run))
					include_once ROOT.$path.$run;
				else
					throw new Error("Required autoload file not found for def $path: $path$run");
		}

		if (isset($al['psr-0']))
			foreach ($al['psr-0'] as $k => $v) {
				$v .= '/';

				if (isset(self::$class[$k]))
					self::$class[] = $path.$v; //append
				else
					self::$class[$k] = $path.$v; //set
			}

		if (isset($al['include-path']))
			set_include_path(get_include_path().PATH_SEPARATOR.ROOT.$path.$al['include-path']);

		if (isset($al['classmap']))
			; //FIXME: SOMEDAY
			//WE'RE GONNA RISE UP ON THAT WIND
			//YOU KNOOOOW
			//SOMEDAY
			//WE'RE GONNA DANCE WITH THOSE LIONS
	}

	/**
	 * Load (register autoloaders) mod by name
	 * self::$mods_def MUST BE ALREADY FILLED!
	 * @throws Error
	 * @param $modname
	 * @return bool success
	 */
	public static function loadMod($modname) {
		if (!empty(self::$mods_loaded[$modname]))
			return true; //already loaded
		self::$mods_loaded[$modname] = true;

		$def = self::$mods_def[$modname];
		$basepath = '/mod/'.$modname.'/';

		if (!$def['def_type'] || $def['def_type'] == 'none')
			$selfdef = [];
		else {
			$jsonpath = $basepath.($def['def_type'] == 'composer' ? 'composer' : 'def').'.json';
			if (($selfdef = self::readJsonFromFile($jsonpath)) === false)
				throw new Error("Could not load self-definition for mod '$modname' (def_type: $def[def_type])");
		}

		if (isset($def['require']))
			foreach ((array) $def['require'] as $v)
				self::loadMod($v);

		//merge config from mods.json with composer/def
		$al = isset($def['autoload']) ? $def['autoload'] : [];
		$al = isset($selfdef['autoload']) ? array_replace_recursive($selfdef['autoload'], $al) : $al;
		if ($al)
			self::parseAutoloader($al, $basepath);

		return true;
	}

	/**
	 * Class freeloader
	 * @param $name of class
	 */
	public static function loadClass($name) {
		//rare case, mostly with manual loading
		if ($name[0] == '\\')
			$name = substr($name, 1);

		$nsmain = explode('\\', $name, 2)[0];

		if (!isset(self::$class[$nsmain]))
			$nsmain .= '\\';

		if (isset(self::$class[$nsmain]))
			$traverse = [self::$class[$nsmain]];
		else
			$traverse = self::$class;

		$nspath = str_replace(['\\','_'], DIRECTORY_SEPARATOR, $name);
		foreach ($traverse as $path)
			if (file_exists(ROOT.$path.$nspath.'.php')) {
				req1(ROOT.$path.$nspath.'.php');
				self::$loaded[$name] = [];
				return true;
			}

		return false;
	}

	/**
	 * Add unloaders for class
	 * @param $class name
	 * @param $unl oading function
	 */
	public static function registerUnload($class, $unl = NULL) {
		if (!isset($unl)) //only unload
			self::$loaded[][] = $class;
		else
			self::$loaded[$class][] = $unl;
	}

	/**
	 * Run unloaders (functions stack) for class
	 * @param $class name
	 * @return bool success
	 */
	public static function unload($class) {
		if (empty(self::$loaded[$class]))
			return false;

		self::runFuncArray([self::$loaded[$class]]);
		return true;
	}

	/**
	 * Unload all known classes (those that have
	 * registered unloader function stack)
	 */
	public static function unloadAll($x = NULL) {
		if (PROCESS_ID && PROCESS_ID != posix_getpid())
			return;

		while (($i = array_pop(self::$loaded)) !== null)
			if ($i)
				self::runFuncArray($i);

		if ($x && CLI)
			echo Console::light_green,
				"K, ThxBye :3\n",
				Console::reset;
	}

	/** Add /sys/ autoloader **/
	public static function sysinit() {
		if (isset(self::$class['r3v\\']))
			return;
		self::$class['r3v\\'] = '/sys/';
	}

	/** Start r3v engine (:D) and load some definitions (^_^) */
	public static function go() {
		if (self::$mods_def)
			return;
		if ((self::$mods_def = self::readJsonFromFile('/mod/mods.json')) === false)
			throw new Error('Unable to load mods definition!');
		unset(self::$mods_def['__example']);

		foreach (self::$mods_def as $name => &$def) {
			//ignored fields: origin, description
			unset($def['origin'], $def['description']);

			//if explicit_load==true then we just don't do anything, leave as it is
			if (empty($def['explicit_load']))
				self::loadMod($name);

			//add route scopes
			if (!empty($def['route'])) {
				$r = $def['route'];
				$basepath = '/mod/'.$name;

				self::$route[$r['scope']] = [
					'dir' => $basepath.(isset($r['dir'])        ? $r['dir'] : '/'),
					'template'      => (isset($r['template'])   ? $basepath.$r['template'] : false),
					'force_path'    => (isset($r['force_path']) ? $r['force_path'] : false),
					'error_page'    => (isset($r['error_page']) ? $r['error_page'] : false),
					'autorun'       => (isset($r['autorun']))   ? $r['autorun'] : [],
				];
			}
		}
		unset($def);

		//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\

		if (CLI)
			Console::start(); //console/cli/REPL mode
		else
			View::go(); //start app in html/http mode
	}
}

/** Scope sandbox */
function req1() {
	require_once func_get_arg(0);
}
