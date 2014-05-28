<?php ///rev engine \rev\CRUD\Object
namespace rev\CRUD;
use rev\Error;

/**
 * CRUD for single object
 * Manages rev\Form instance, handles field hooks
 */
class Object {
	protected $def;

	public $id = null;

	protected $values = [];
	protected $dbo = null;
	protected $form = null;
	protected $cache = [];

	function __construct($def) {
		unset($def['fields']['id']);
		$this->def = $def;
		unset($this->def['fields']['submit']);

		$this->dbo = new \rev\DB\Table($def['table']);

		$this->form = new \rev\Form($def);
	}

	/**
	 * Launch hooks by field name
	 * @param $name field's property name
	 */
	protected function _processHooks($name) {
		foreach ($this->def['fields'] as $k => $v)
			if (!empty($v[$name])) {
				$cmd = is_array($v[$name]) ? $v[$name] : explode(' ', $v[$name], 2);

				if ($cmd[0] == 'set')
					switch (strtolower($cmd[1])) {
						case 'now':
							$this->values[$k] = NOW;
							break;
						case 'null':
							$this->values[$k] = null;
							break;
					}
				elseif ($cmd[0] == 'call') {
					if (!is_callable($cmd[1]))
						throw new Error("CRUD: Hook $name for field $k is not callable");
					call_user_func_array($cmd[1], [&$this->values[$k], &$this->values]);
				}
			}
	}

	public function exists($id, $force = false) {
		if (!(is_int($id) || (is_string($id) && ctype_digit($id))))
			return false;

		if (array_key_exists('values', $this->cache) && !$force)
			return !!$this->cache['values'];

		$vals = $this->dbo->select()->where(['id' => $id, $this->def['select_where']])->row();
		$this->dbo->clear();
		if (!$vals)
			return false;

		$this->cache['values'] = $vals;
		return true;
	}

	public function load($id) {
		if (!$this->exists($id))
			return false;

		if ($this->values = $this->cache['values']) {
			unset($this->values['id']);
			$this->id = $id;
			$this->form->values = $this->values;
		}
		unset($this->cache['values']);
		return !empty($this->values);
	}

	public function formToLocal() {
		$this->values = $this->form->values;
		unset($this->values['submit'], $this->cache['values']);
		return $this;
	}

	public function save() {
		$this->_processHooks('pre_save');

		if ($this->id)
			$this->dbo->where(['id' => $this->id])->update($this->values)->exec();
		else
			$this->id = $this->dbo->insert($this->values)->iid();
		$this->dbo->clear();
	}

	public function delete() {
		if ($this->id) {
			$this->dbo->delete()->where(['id' => $this->id])->exec();
			$this->dbo->clear();
		}
	}

	public function create() {
		//fill with empty data
		$this->values = array_fill_keys(array_keys($this->def['fields']), null);

		$this->_processHooks('on_create');

		$this->form->values = $this->values;
		$this->id = null;
	}

	public function __get($name) {
		if ($name == 'values' || $name == 'form')
			return $this->{$name};
		if ($name == 'submitted')
			return $this->form->submitted;
		throw new Error("CRUD Obj: Property $name is inaccessible");
	}

	public function __set($name, $val) {
		if ($name == 'values' && is_array($val)) {
			foreach ($val as $k => $v)
				if (array_key_exists($k, $this->values))
					$this->values[$k] = $v;
		}
		throw new Error("CRUD Obj: Property $name is inaccessible");
	}
}
