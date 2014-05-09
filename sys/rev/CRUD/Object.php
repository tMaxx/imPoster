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
	protected $form = null;

	function __construct($def) {
		$this->def = $def;
		$this->dbo = new \rev\DB\Table($def['table']);
	}

	public function load($id) {
		$this->fields = $this->dbo->select()->where(['id' => $id])->row();
		unset($this->fields['id']);
		if (!$this->fields)
			throw new Error("CRUD Obj: load: no results for $id at $def[name]");
		$this->id = $id;

		return $this;
	}

	/** Launch pre-save hooks */
	protected function _preSave() {
		foreach ($this->def['fields'] as $k => $v) {
			if (isset($v['pre_save']))
			switch ($v['type']) {
				case 'ts':
					if ($v['pre_save'] == 'set now')
						$this->fields[$k] = NOW;
					break;
			}
		}
	}

	public function save() {
		if ($this->id)
			$this->dbo->where(['id' => $id])->update($this->fields)->exec();
		else {
			$this->id = $this->dbo->insert($this->fields)->iid();
		}
		return $this;
	}

	public function delete() {
		$this->dbo->delete()->where(['id' => $id])->exec();
		return $this;
	}

	public function new() {
		//fill with empty data
		$this->fields = array_fill_keys(array_keys($def['fields']), null);
		//TODO: all

		return $this;
	}

	public function render() {
		// code...
	}

	public function __get($name) {
		if ($name == 'id')
			return $this->id;
		if (!isset($this->fields[$name]))
			throw new Error("CRUD Obj: no such field name: $name");
		return $this->fields[$name];
	}

	public function __set($name, $val) {
		if ($name == 'id')
			$this->id = $val;
		elseif (!isset($this->fields[$name]))
			throw new Error("CRUD Obj: no such field name: $name");
		return ($this->fields[$name] = $val);
	}
}
