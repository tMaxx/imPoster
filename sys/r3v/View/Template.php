<?php ///r3v engine \r3v\View\Template
namespace r3v\View;
use \r3v\File;

/**
 * Template creating
 */
class Template {

	private $src;
	private $type;
	private $replace;

	function __construct($source, $type = 'file') {
		$this->type = $type;
		$this->src = $source;
	}

	/** Set replace patterns **/
	public function replace(array $in) {
		$this->replace = $in;
		return $this;
	}

	/** Parse template and return result */
	public function get() {
		if ($this->type == 'file') {
			$cont = File::contents($this->src);
			if ($cont === false)
				throw new \Error("File not found: {$this->src}");
		} elseif ($this->type == 'raw')
			$cont = $this->src;
		else
			throw new \Error("Unknown type: {$this->type}");

		$left = [];
		$right = [];

		foreach ($this->replace as $k => $v) {
			$left[] = '<%'.$k.'%>';
			$right[] = $v;
		}

		$cont = str_replace($left, $right, $cont);

		return $cont;
	}

	/** Parse and echo template **/
	public function echoo() {
		echo $this->get();
	}

}
