<?php ///r3v engine \r3v\DB\Table
namespace r3v\DB;

/**
 * Table - query builder/repository
 */
class Table extends Base {
	const MODE_NONE = 0;
	const MODE_DELETE = 1;
	const MODE_INSERT = 2;
	const MODE_UPDATE = 3;
	const MODE_SELECT = 4;
	protected $mode = self::MODE_NONE;

	protected $table = '';

	protected $data = array();

	protected $fields = array();
	protected $where = array();
	protected $orderby = array();
	protected $end = array();

	function __construct($tab) {
		$this->table = $tab;
	}

	function guard_mode($mode) {
		if ($this->mode != $mode)
			throw new Error('Incorrect mode for this operation');
	}

	function guard_notmode($mode) {
		if ($this->mode == $mode)
			throw new Error('Incorrect mode for this operation');
	}

	public function delete() {
		$this->mode = self::MODE_DELETE;
		return $this;
	}

	public function insert($data, $fields = '') {
		$this->mode = self::MODE_INSERT;
		$this->data = $data;
		if ($fields)
			$this->fields[] = $fields;
		elseif (!isset($data[0]))
			$this->fields = array_keys($data);
		return $this;
	}

	public function update($data) {
		$this->mode = self::MODE_UPDATE;
		$this->data = $data;
		return $this;
	}

	public function select($fields = '*') {
		$this->mode = self::MODE_SELECT;
		$this->fields[] = $fields;
		return $this;
	}

	public function fields($fields, $replace = FALSE) {
		$this->guard_mode(self::MODE_INSERT);
		$this->guard_mode(self::MODE_SELECT);
		if ($replace)
			$this->fields = array();

		$this->fields[] = $fields;
		return $this;
	}

	public function where($arg, $replace = FALSE) {
		$this->guard_notmode(self::MODE_INSERT);
		if ($replace)
			$this->where = array();

		$this->where[] = $arg;
		return $this;
	}

	public function orderby($arg, $replace = FALSE) {
		$this->guard_mode(self::MODE_SELECT);
		if ($replace)
			$this->orderby = array();

		$this->orderby[] = $arg;
		return $this;
	}

	protected function createquery() {
		$parts = array();

		// insert: table(fields), values
		// update: table, set, where
		// delete: table, where
		// select: wtf

		switch ($this->mode) {
			case self::MODE_INSERT: {
				$parts[] = 'INSERT INTO '.$this->table;
				if ($this->fields)
					$parts[] = '('.implode(', ', $this->fields).')';
				$parts[] = 'VALUES';
				$parts[] = '(' . $this->implode(', ', $this->data, true, true, '), (') . ')';
				break;
			}
			case self::MODE_SELECT: {
				$parts[] = 'SELECT';
				$parts[] = implode(', ', $this->fields);
				$parts[] = 'FROM '.$this->table;
				if ($this->where) {
					$parts[] = 'WHERE';
					$parts[] = $this->implode(' AND ', $this->where, true);
				}
				if ($this->orderby) {
					$parts[] = 'ORDER BY';
					$parts[] = implode(', ', $this->orderby);
				}
				break;
			}
			case self::MODE_DELETE: {
				$parts[] = 'DELETE FROM '.$this->table;
				if ($this->where) {
					$parts[] = 'WHERE';
					$parts[] = $this->implode(' AND ', $this->where, true);
				}
				break;
			}
			case self::MODE_UPDATE: {
				$parts[] = 'UPDATE '.$this->table;
				$parts[] = 'SET';
				$parts[] = $this->implode(', ', $this->data, true, true);
				if ($this->where) {
					$parts[] = 'WHERE';
					$parts[] = $this->implode(' AND ', $this->where, true);
				}
				break;
			}
			default:
				throw new Error('Table: mode not specified');
				return;
		}

		$this->query = implode(' ', $parts);
	}
}