<?php ///rev engine \rev\CRUD\Object
namespace rev\CRUD;
use rev\Error;

/**
 * CRUD single object
 */
class Object {
	protected $def;

	protected $fields = [];
	protected $id = null;
	protected $dbo = null;

	function __construct($def) {
		$this->def = $def;
		$this->dbo = new \rev\DB\Table($def['table']);
	}

	public function load($id) {
		$this->fields = $this->dbo->select()->where(['id' => $id])->row();
		$this->id = $id;
	}

	public function save() {
		// code...
	}

	public function delete() {
		// code...
	}

	public function new() {
		//fill with empty data
		$this->fields = array_fill_keys(array_keys($def['fields']), null);
	}

	public function render() {
		// code...
	}

	public function __get($name) {
		if (!isset($this->fields[$name]))
			throw new Error("CRUD Obj: no such field name: $name");
		return $this->fields[$name];
	}

	public function __set($name, $val) {
		if (!isset($this->fields[$name]))
			throw new Error("CRUD Obj: no such field name: $name");
		return ($this->fields[$name] = $val);
	}
}