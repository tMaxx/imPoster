<?php ///r3vCMS \r3v\View\Explicit
namespace r3v\View;
use \r3v\File;

/**
 * Explicit view loader - mostly static stuff
 * MVC For The Win!
 */
class Explicit {
	protected $vars = [];
	protected $basepath = '';
	protected $node = '';

	protected function inc() {
		foreach (func_get_arg(0) as $k => $v)
			${$k} = $v;
		return (include ROOT.func_get_arg(1));
	}

	public function __construct($basepath, $node, $vars = []) {
		$this->basepath = $basepath;
		$this->node = $node;
		$this->vars = $vars;
	}

	public function go() {
		$route = $this->basepath.'control/'.$this->node;

		if (File::fileExists($route.'.php'))
			$route .= ($extension = '.php');
		elseif (File::fileExists($route.'/index.php'))
			$route .= ($extension = '/index.php');
		else
			throw new \Error404("Node \"{$this->node}\" not found");

		$ret = $this->inc($this->vars, $route);
		$this->vars = [];
		unset($route);

		if (is_array($ret)) {
			if (!isset($ret[0]) || is_string($ret[0])) {
				$view = $this->basepath.'view/'.(isset($ret[0]) ? $ret[0].'.php' : $this->node.$extension);
				if (!$view)
					return;

				$this->inc($ret, $view);

			} elseif (isset($ret[0]) && is_int($ret[0])) {
				$view = '\\Error'.$view;
				if (isset($ret[1]))
					throw (new $view($ret[1]));
				throw (new $view());
			}
		}
		$this->vars = [];
	}
}
