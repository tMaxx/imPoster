<?php ///r3vCMS \r3v\View\Template
namespace r3v\View;

/**
 * Template creating
 */
class Template {

	private $src;
	private $type;
	private $replace;

	function __construct($source, $type = 'file') {
		$this->type = $type;
		$this->source = $source;
	}

	public function replace(array $in) {
		$this->replace = $in;
	}

	public function get() {
		if ($this->type == 'file') {
			$cont = File::contents($this->src);
			if ($cont === false)
				throw new Error("File not found: {$this->src}");
		} elseif ($this->type == 'raw')
			$cont = $this->src;
		else
			throw new Error("Unknown type: {$this->type}");


		$ls = [];
		$rs = [];

		foreach ($this->replace as $k => $v) {
			$ls[] = '<%'.$k.'%>';
			$rs[] = $v;
		}

		str_replace($ls, $rs, $cont);

		return $cont;
	}

	public function echo() {
		echo $this->get();
	}

}
