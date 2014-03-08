<?php ///r3vCMS /sys/Mod.php
namespace r3v;

/**
 * Modloader class
 * Handler for class loading, service handling
 */
class Mod {
	protected static $class = [];

	protected static $service = [];
	protected static $view = [];

	protected static $loaded = [];
	protected static $mods = [];

	/**
	 * Load definition
	 * @param $path of def
	 * @param $returnDef instead of bool
	 * @return bool success|array
	 */
	public static function loadDef($path, $returnDef = false) {
		if (!$path)
			return false;

		$path = str_replace(['..', '~'], '', $path);

		if (!is_file(ROOT.$path))
			return false;
		if (($def = file_get_contents(ROOT.$path)) === false)
			return false;
		if (!($def = json_decode($def, true)))
			return false;
		if (!isset($def['r3v']))
			return false;

		$def = $def['r3v'];
		$path = dirname($path) . (isset($def['basepath']) ? $def['basepath'] : '') . '/';

		$insert =
			function($arr, $data, $prefix) {
				foreach ($data as $name => $def) {
					if (isset(static::${$arr}[$name]) || !$def)
						continue;

					if (is_string($def))
						$def = ['file' => $def];
					elseif (!is_array($def))
						throw new Error("Invalid insert for $arr $name: !('' || [])");

					if (isset($def['file'])) {
						if (!file_exists(ROOT.$prefix.$def['file']))
							throw new Error("File $prefix$def[file] does not exist for $arr $def");
						$def['file'] = $prefix.$def['file'];
					}

					static::${$arr}[$name] = $def;
				}
			};

		if (isset($def['view']))
			$insert('view', $def['view'], $path);

		if (isset($def['service']))
			$insert('service', $def['service'], $path);

		if ($def['autoload']) {
			$vals = $def['autoload']; //fixme
			if (isset($vals['files'])) {
				if (is_string($vals['files']))
					$vals['files'] = (array) $vals['files'];

				foreach ($vals['files'] as $run)
					if (is_file(ROOT.$path.$run))
						include_once ROOT.$path.$run;
					else
						throw new Error('Required autoload run not found for def $path: $path$run');
			}
			if (isset($vals['psr-0']))
				foreach ($vals['psr-0'] as $v)
					self::$class[] = $path.$v;
		}

		if (isset($def['autorun']))
			self::runFuncArray($arr);

		return $returnDef ? $def : TRUE;
	}

	/**
	 * Load mod by name
	 * @param $modname
	 * @return bool success
	 */
	public static function loadMod($modname) {
		if ($r = self::loadDef('/mod/'.$modname.'/def.json', true))
			self::$mods[$modname] = $r;
		return !!$r;
	}

	public static function runFuncArray($arr) {
		foreach ((array)$arr as $a) {
			$a = (array) $a;
			$f = array_shift($a);
			if (is_callable($f))
				call_user_func_array($f, $a);
		}
	}

	/**
	 * Class freeloader
	 * @param $name of class
	 */
	public static function load($name) {
		$ns = str_replace('\\', '/', $name);
		foreach (self::$class as $path)
			if (file_exists(ROOT.$path.$ns.'.php')) {
				require_once ROOT.$path.$ns.'.php';
				self::$loaded[$name] = [];
				return true;
			}

		return false;
	}

	/**
	 * Add unloaders for class
	 * @param $class name
	 * @param $unl oading fun
	 */
	public static function registerUnload($class, $unl) {
		self::$loaded[$name][] = $unl;
	}

	/**
	 * Run unloaders (functions stack) for class
	 * @param $class name
	 */
	public static function unload($class) {
		if (!isset(self::$loaded[$class]) || is_bool(self::$loaded[$class]))
			return;

		self::runFuncArray([self::$loaded[$class]]);
	}

	/**
	 * Unload all known classes (those that have
	 * registered unloader function stack)
	 */
	public static function unloadAll() {
		while ($i = array_popk(self::$loaded))
			self::unload(key($i));
		if ($x && CLI)
			echo "ThxBye :3\n";
	}

	public static function checkServices() {
		$path = Vars::uri('r3v/path');
		if (substr($path, 0, 5) != '/r3v/')
			return false;
		$dirs = CMS::scandir('/mod/');
		foreach ($dirs as $d)
			self::loadDef($d);

		$path = substr($path, 5);
		if (!isset(self::$service[$path]))
			return false;

		ob_start();
		CMS::safeIncludeOnce(self::$service[$path]);
		ob_end_flush();

		return true;
	}

	public static function entrypoint() {
		if (CLI) {
			echo "We come in interactive mode :D\n", r3v_ID, ' // loaded in ', ms_from_start(), "ms";
			if (!class_exists('\\Boris\\Boris')) {
				self::loadMod('boris');
				echo " // Boris REPL v".\Boris\Boris::VERSION."\n";
				$boris = new \Boris\Boris('r3v> ');
				$boris->start();
			} else
				echo "\n";
			return;
		}
		if (self::lock())
			throw Error505();

		ob_start();

		if (!self::checkServices())
			View::go();

		ob_end_flush();

		self::unloadAll();
	}
}
