<?php ///rev engine \rev\CRUD\Object
namespace rev\CRUD;
use rev\Error;

/** CRUD single object */
class Object {
	protected $def;

	protected $fields = [];
	protected $id = null;
	protected $dbo = null;
	protected $form = null;

	function __construct($def) {
		$this->def = $def;
		unset($this->def['fields']['submit']);

		$this->dbo = new \rev\DB\Table($def['table']);

		unset($def['fields']['id']);
		$this->form = new \rev\Form($def);
		if ($this->form->submitted) {
			$ff = $this->form->fields;
			foreach ($this->fields as $k => $_)
				$this->fields[$k] = $ff[$k]->raw_value;
		}
	}

	/**
	 * Launch hooks by field name
	 * @param $name field's property name
	 */
	protected function _processHooks($name) {
		foreach ($this->def['fields'] as $k => $v)
			if (!empty($v[$name])) {
				$cmd = explode(' ', $v[$name], 2);

				if ($cmd[0] == 'set') {
					if (strtolower($cmd[1]) == 'now')
						$this->fields[$k] = NOW;
				} elseif ($cmd[0] == 'call') {
					call_user_func($cmd[1], $this->fields[$k]);
				}
			}
	}

	public function load($id) {
		$this->fields = $this->dbo->select()->where(['id' => $id])->row();
		unset($this->fields['id']);
		if (!$this->fields)
			throw new Error("CRUD Obj: load: no results for $id at $def[name]");
		$this->id = $id;

		return $this;
	}

	public function save() {
		$this->_processHooks('pre_save');
		if ($this->id)
			$this->dbo->where(['id' => $this->id])->update($this->fields)->exec();
		else
			$this->id = $this->dbo->insert($this->fields)->iid();

		return $this;
	}

	public function delete() {
		if ($this->id)
			$this->dbo->delete()->where(['id' => $this->id])->exec();
		return $this;
	}

	public function create() {
		//fill with empty data
		$this->fields = array_fill_keys(array_keys($def['fields']), null);
		$this->id = null;
		//TODO: all
		$this->_processHooks('on_create');

		return $this;
	}

	public function render() {
		$this->form->render();
	}

	public function __get($name) {
		if ($name == 'id')
			return $this->id;
		if ($name == 'submitted')
			return $this->form->submitted;
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
