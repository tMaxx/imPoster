<?php ///revCMS /sys/DB.php
namespace {
///DB factory
function DB($var) {
	if (is_object($var) && ($var instanceof Model))// || ($var instanceof CMS\DB\Saveable))
		return new CMS\DB\Instance($var);
	elseif (is_string($var)) {
		if (substr_count($var, ' ') == 0)
			return new CMS\DB\Table($var);
		return new CMS\DB\Base($var);
	} else
		throw new CMS\DB\Error('Unsupported $var type');
}
}
namespace CMS\DB {
class Error extends \Error {}

///Make a class saveable via DB\Instance
interface Saveable {
	public function toArray();
	public static function getKeyName();
	public function getId();
	public function getTableName();
}

/**
 * Base - Database support class
 */
class Base extends \_Locks {
	//db object
	private static $db = NULL;
	///compiled query
	protected $query = '';
	///statement
	protected $stmt = null;
	protected $stmt_types = '';
	protected $stmt_param = array();
	///last query's result
	protected $query_result = null;
	///db state after last query
	protected $db_status = array();

	/**
	 * Prepare the DB and connect to it
	 * @param $con connection options
	 */
	public static function go($con) {
		if (self::lock())
			return;

		if (!isset($con['host']) || !isset($con['user']) || !isset($con['pass']) || !isset($con['dbname']))
			throw new Error('Not sufficient connection parameters!');

		self::$db = new \mysqli($con['host'], $con['user'], $con['pass'], $con['dbname']);

		if (self::$db->connect_error)
			throw new Error('Connecting error: '.self::$db->connect_errno);

		if(!self::$db->set_charset("utf8"))
			throw new Error('Set charset failed');
	}

	///End execution, close everything
	public static function end() {
		if (self::lock())
			return;

		if (isset(self::$db)) {
			self::$db->close();
			self::$db = NULL;
		}
	}

	function __construct($var) {
		$this->query = $var;
	}

	function __destruct() {
		$this->query_result = null;
	}

	/**
	 * Return no of rows affected by last query
	 * @return int
	 */
	public function getAffectedRows() {
		return $this->db_status['affected_rows'];
	}

	/**
	 * Return id of last insert
	 * @return int
	 */
	public function getInsertID() {
		return $this->db_status['insert_id'];
	}

	/**
	 * Return field count of last query
	 * @return int
	 */
	public function getFieldCount() {
		return $this->db_status['field_counts'];
	}

	///Get DB state after action
	protected function retrieveState() {
		$this->db_status['affected_rows'] = self::$db->affected_rows;
		$this->db_status['field_count'] = self::$db->field_count;
		$this->db_status['insert_id'] = self::$db->insert_id;
		return $this;
	}

	// ==================================================================
	//
	// Query builders, handlers, internals
	//
	// ------------------------------------------------------------------

	protected function implode($glue, array $arr, $parametrize = FALSE, $setter = FALSE, $iglue = '') {
		$r = array();

		foreach ($arr as $k => $v)
			if (is_array($v)){
				$el = '';
				if ($iglue) {
					$el = array_pop($r);
					$el = ($el ? $el : '').$iglue;
				}
				$r[] = $el.$this->implode($glue, $v, $parametrize, $setter, $iglue);
			} elseif (is_numeric($k)) {
				if ($iglue && $parametrize) {
					$r[] = '?';
					$this->param($v);
				} else
					$r[] = $v;
			} else {
				if ($v === NULL && !$setter && !iglue)
					$k .= ' is null';
				elseif ($parametrize) {
					if ($iglue)
						$k = '?';
					else
						$k .= '=?';
					$this->param($v);
				} else
					$k .= '=' . $v;
				$r[] = $k;
			}
		
		return implode($glue, $r);
	}

	/**
	 * Add param t.b. processed later
	 * @param $type of value
	 * @param $val
	 * @return this
	 */
	public function param($type, $val = NULL) {
		if ($val === NULL) {
			$val = $type;
			$type = strtolower(gettype($val)[0]);
		}
		$this->stmt_types .= $type;
		$this->stmt_param[] = $val;
		return $this;
	}

	/**
	 * Add params to be processed later
	 * @param $types of values
	 * @param $values
	 * @return this
	 */
	public function params($types, array $values = array()) {
		if (is_array($types))
			foreach ($types as $v)
				$this->param($v);
		else
			for ($i = 0, $c = count($val); $i < $c; $i++)
				$this->param($types[$i], $values[$i]);
		return $this;
	}

	///Prepare all queries
	protected function bindquery() {
		if (!$this->stmt) {
			if (method_exists($this, 'createquery'))
				$this->createquery();

			if (!$this->stmt_types || !$this->stmt_param)
				return FALSE;

			$types = $this->stmt_types;
			$values = $this->stmt_param;
			if (strlen($types) != ($c = count($values)))
				throw new Error('Number of types != number of values!');

			if (!($this->stmt = self::$db->prepare($this->query)))
				throw new Error('Mishap while preparing query');

			if ($c != $this->stmt->param_count)
				throw new Error('Number query params != number of values');

			for ($i = 0; $i < $c; $i++) {
				switch ($types[$i]) {
					case 'i':
						$values[$i] = (int) $values[$i];
						break;
					case 'd':
						$values[$i] = (double) $values[$i];
						break;
					case 'f':
						$values[$i] = (float) $values[$i];
						break;
					case 'c':
						$values[$i] = (string) $values[$i][0];
					case 's':
					default:
						$values[$i] = self::$db->escape_string($values[$i]);
						break;
				}

				$this->stmt->bind_param($types[$i], $values[$i]);
			}
		}
		return TRUE;
	}

	///Compile and execute query
	public function exec() {
		if (!$this->query_result) {
			if ($this->bindquery()) {
				if (!($this->stmt->execute()))
					throw new Error('Query execution unsuccessful');
				$this->query_result = new Result($this->stmt);
			} else {
				if (!$this->query)
					throw new Error('Query not specified!');
				$this->stmt = NULL;
				if (($this->query_result = self::$db->query($this->query)) === FALSE)
					throw new Error('Query execution unsuccessful: "'.$this->query.'"');
			}
			$this->retrieveState();
		}
		return $this;
	}

	// ==================================================================
	//
	// Result handling functions
	//
	// ------------------------------------------------------------------

	///Fetch a single row from db
	public function row() {
		return $this->exec()->query_result->fetch_assoc();
	}

	///Fetch a set of rows from db
	public function rows() {
		$this->exec();
		$r = array();
		if (method_exists('mysqli_result', 'fetch_all'))
			$r = $this->query_result->fetch_all(MYSQLI_ASSOC);
		else
			while($tmp = $this->query_result->fetch_assoc())
				$r[] = $tmp;

		return $r;
	}

	public function rowsBy($key) {
		$q = $this->rows();
		$r = array();

		foreach ($q as $v)
			$r[$v[$key]] = $v;

		return $r;
	}

	public function obj($type = NULL) {
		if (!$type && $this instanceof Table)
			$type = $this->table;

		return new $type($this->row());
	}

	public function objs($type = NULL) {
		if (!$type && $this instanceof Table)
			$type = $this->table;
		
		$r = $this->rows();
		foreach ($r as &$v)
			$v = new $type($v);
		reset($r);

		return $r;
	}

	public function num() {
		return $this->exec()->query_result->fetch_row();
	}

	public function nums() {
		$this->exec();
		$r = array();
		if (method_exists($this->query_result, 'fetch_all'))
			$r = $this->query_result->fetch_all(MYSQLI_NUM);
		else
			while($tmp = $this->query_result->fetch_row())
				$r[] = $tmp;

		return $r;
	}

	public function pairs() {
		$q = $this->nums();
		$r = array();
		foreach ($q as $v)
			$r[$v[0]] = $v[1];
		return $r;
	}

}

/**
 * DBresult
 * Patch for missing mysqli_stmt->get_result()
 */
class Result {
	protected $stmt = NULL;
	protected $last = NULL;
	protected $fields_assoc = array();
	protected $fields_num = array();
	protected $result_fields = array();

	/**
	 * Constructor
	 * @param $statement \mysqli_stmt
	 */
	function __construct(&$statement) {
		$this->stmt = &$statement;
		if ($meta = $this->stmt->result_metadata()) {
			$i = 0;
			$fields = $meta->fetch_fields();
			foreach ($fields as $v) {
				$this->fields_assoc[$v->name] = NULL;
				$this->fields_num[$i] = NULL;
				$i++;
			}
		} else
			throw new Error('Could not get statement metadata');
	}

	function __destruct() {
		$this->free();
	}

	public function free() {
		if ($this->stmt) {
			$this->stmt->close();
			$this->stmt = null;
		}
	}

	protected function next($type) {
		if ($this->last != $type) {
			$this->query_result_fields = array();
			if ($type == MYSQLI_ASSOC)
				foreach ($this->fields_assoc as $k => $_)
					$this->query_result_fields[] = &$this->fields_assoc[$k];
			else
				foreach ($this->fields_num as $k => $_)
					$this->query_result_fields[] = &$this->fields_num[$k];

			$this->last = $type;
			if (!call_user_func_array(array($this->stmt, 'bind_result'), $this->query_result_fields))
				throw new Error('Could not bind result set');
		}

		if (call_user_func(array($this->stmt, 'fetch')) === NULL)
			return NULL;

		return $this;
	}

	public function fetch_row() {
		$r = $this->next(MYSQLI_NUM);
		return $r !== NULL ? array_copy($this->fields_num) : NULL;
	}

	public function fetch_assoc() {
		$r = $this->next(MYSQLI_ASSOC);
		return $r !== NULL ? array_copy($this->fields_assoc) : NULL;
	}

	public function fetch_all($type) {
		if ($type == MYSQLI_NUM)
			$fun = 'fetch_row';
		else
			$fun = 'fetch_assoc';

		$r = array();
		while ($q = $this->$fun())
			$r[] = $q;

		return $r;
	}
}

/**
 * Instance - model instance handler
 */
class Instance extends Base {
	protected $inst;

	function __construct(&$inst) {
		$this->inst = &$inst;
	}

	public function __destruct() {
		$this->inst = NULL;
	}

	public function save() {
		if (!$inst->getId())
			return $this->insert();

		if (method_exists($this->inst, 'preSave'))
			$this->inst->preSave();

		$vals = $this->inst->toArray();
		$pk_key = (array) $this->inst->getKeyName();
		foreach ($pk_key as $v)
			unset($vals[$v]);
		$pk_val = (array) $this->inst->getId();

		$pks_list = $this->implode(' AND ', array_combine($pk_key, $pk_val), true);
		$vals_list = $this->implode(', ', $vals, true, true);

		$this->query = 'UPDATE '.$this->inst->getTableName().' SET '.$vals_list.' WHERE '.$pks_list;
		$this->exec();

		if (method_exists($this->inst, 'postSave'))
			$this->inst->postSave();
		return $this;
	}

	public function insert() {
		if (method_exists($this->inst, 'preInsert'))
			$this->inst->preInsert();

		$vals = $this->inst->toArray();
		$pks = (array) $this->inst->getKeyName();
		foreach ($pks as $v)
			unset($vals[$v]);
		unset($pks, $v);

		$val_args = implode(', ', array_keys($vals));
		$val_list = $this->implode(', ', $vals, true, true, ', ');

		$this->query = 'INSERT INTO '.$this->inst->getTableName().'('.$val_args.') VALUES ('.$val_list.')';
		$this->exec();

		if (method_exists($this->inst, 'postInsert'))
			$this->inst->postInsert();
		return $this;
	}

	public function remove() {
		if (method_exists($this->inst, 'preRemove'))
			$this->inst->preRemove();

		$pks = (array) $this->inst->getKeyName();

		$pks_list = $this->implode(' AND ', $pks, true);

		$this->query = 'DELETE FROM '.$this->inst->getTableName().' WHERE '.$pks_list;
		$this->exec();

		if (method_exists($this->inst, 'postRemove'))
			$this->inst->postRemove();
		$this->inst = NULL;
		return $this;
	}
}

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
		return $this;
	}

	public function update($data) {
		$this->mode = self::MODE_UPDATE;
		$this->data = $data;
		return $this;
	}

	public function select($fields) {
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
		}

		$this->query = implode(' ', $parts);
	}
}
} //namespace CMS\DB
