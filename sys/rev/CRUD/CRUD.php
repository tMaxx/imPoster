<?php ///rev engine \rev\CRUD\CRUD
namespace rev\CRUD;
use \rev\Error, \rev\DB\Q;

/**
 * CRUD - basic Create/Read/Update/Delete
 */
class CRUD {
	protected $path = '';
	protected $def = [];
	protected $object = null;

	public function __construct($path) {
		$path = rev\View::getCurrentBasepath() . 'crud/' . $path . '.json';

		$this->def = rev\File::jsonFromFile($path);
		if ($this->def === false)
			throw new Error("CRUD: invalid path for definition {$path}");
	}

	public function object() {
		return $this->object;
	}

	public function navigation() {
		// code...
	}

	public function page($pnum) {
		// Q('SELECT COUNT(');
	}
}
