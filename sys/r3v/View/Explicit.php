<?php ///r3vCMS \r3v\View\Explicit
namespace r3v\View;
use \r3v\File;

/**
 * Explicit view loader - mostly static stuff
 *
 */
class Explicit {
	protected $vars = [];
	protected $basepath = '';
	protected $node = '';

	protected function inc() {
		foreach ($this->vars as $k => $v)
			${$k} = $v;
		$this->vars = array();
		return (include ROOT.$this->node);
	}

	public function __construct($basepath, $node, $vars = []) {
		$this->basepath = $basepath;
		$this->node = $node;
		$this->vars = $vars;
	}

	public function go() {
		$p = $this->basepath.'/'.$this->node;

		if (File::fileExists($p.'.php'))
			$this->node = $p.'.php';
		elseif (File::fileExists($p.'/index.php'))
			$this->node = $p.'/index.php';
		else
			throw new \Error404("Node \"{$p}\" not found");

		//file exists, we know now everything needed.
		$ret = $this->inc();

		if (is_array($ret)) {
			// $template = new View\Template($this->basepath.$ret[0]);

			return $this->basepath.$ret[0];
		}

		//TODO: redirects, features and 'things'
		//Also: templates
	}

	public function render() {
		// code...
	}
}

/*

dostajesz na start ścieżkę startową
	"idź, i tam wyrenderuj"
no to renderujesz node, które dostałeś.
*/

