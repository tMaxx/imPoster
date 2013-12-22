<?php ///revCMS /sys/DB.php
///DB factory
function DB($var) {
	if (($var instanceof Model))// || (is_object($var) && method_exists($var, 'toArray') && method_exists($var, 'set')))
		return new DBinst($var);
	elseif (is_string($var)) {
		if (substr_count($var, ' ') == 0)
			return new DBtabl($var);
		return new DB($var);
	} else
		throw new ErrorDB('Unsupported $var type');
}

/**
 * DB - Database support class
 */
class DB extends _Locks {
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
			throw new ErrorDB('Not sufficient connection parameters!');

		self::$db = new mysqli($con['host'], $con['user'], $con['pass'], $con['dbname']);

		if (self::$db->connect_error)
			throw new ErrorDB('Error while connecting: '.self::$db->connect_errno);

		if(!self::$db->set_charset("utf8"))
			throw new ErrorDB('Error while setting charset');
	}

	///End execution, close everything
	public static function end() {
		if (self::lock())
			return;

		if (isset(self::$db)){
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

	protected function implode($glue, array $arr, $parametrize = FALSE) {
		$r = array();

		//prepare keys
		foreach ($arr as $k => $v)
			if (is_numeric($k))
				$r[] = $v;
			else {
				if ($v === NULL)
					$k .= ' is null';
				elseif ($parametrize) {
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
				throw new ErrorDB('Number of types != number of values!');

			if (!($this->stmt = self::$db->prepare($this->query)))
				throw new ErrorDB('Error while preparing query');

			if ($c != $this->stmt->param_count)
				throw new ErrorDB('Number query params != number of values');

			for ($i = 0; $i < $c; $i++) {
				switch ($types[i]) {
					case 'i':
						$val[$i] = (int) $val[$i];
						break;
					case 'd':
						$val[$i] = (double) $val[$i];
						break;
					case 'f':
						$val[$i] = (float) $val[$i];
						break;
					case 'c':
						$val[$i] = (string) $val[$i][0];
					case 's':
					default:
						$val[$i] = self::$db->escape_string($val[$i]);
						break;
				}

				$this->stmt->bind_param($types[$i], $val[$i]);
			}
		}
		return TRUE;
	}

	///Compile and execute query
	public function exec() {
		if (!$this->query_result) {
			if ($this->bindquery()) {
				if (!($this->stmt->execute()))
					throw new ErrorDB('DB: Error while executing query');
				$this->query_result = new DBresult($this->stmt);
			} else {
				if (!$this->query)
					throw new ErrorDB('Query not specified!');
				$this->stmt = NULL;
				$this->query_result = self::$db->query($this->query);
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
		if (!$type && $this instanceof DBtabl)
			$type = $this->c['table'];

		return new $type($this->row());
	}

	public function objs($type = NULL) {
		if (!$type && $this instanceof DBtabl)
			$type = $this->c['table'];
		
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
class DBresult {
	protected $stmt = NULL;
	protected $last = NULL;
	protected $fields_assoc = array();
	protected $fields_num = array();
	protected $result_fields = array();

	/**
	 * Constructor
	 * @param $statement mysqli_stmt
	 * @return NULL|mysqli_result
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
			return true;
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
				throw new ErrorDB('Error binding result set');
		}

		if (call_user_func(array($this->stmt, 'fetch')) === NULL)
			return NULL;

		return $this;
	}

	public function fetch_row() {
		return $this->next(MYSQLI_NUM)->fields_num;
	}

	public function fetch_assoc() {
		return $this->next(MYSQLI_ASSOC)->fields_assoc;
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
 * DBinst - model instance handler
 */
class DBinst extends DB {
	protected $inst = NULL;
	protected $table = '';	

	function __construct(&$inst) {
		$this->inst = &$inst;
		$this->table = $inst->table();
	}

	public function __destruct() {
		$this->inst = NULL;
	}

	public function save() {
		if (!$inst->getId())
			return $this->insert();

		if (method_exists($inst, 'preSave'))
			$inst->preSave();

		$vals = $this->inst->toArray();
		$pk_key = (array) $this->inst->getPK();
		foreach ($pk_key as $v)
			unset($vals[$v]);
		$pk_val = (array) $this->inst->getId();

		$pks_list = $this->implode(' AND ', array_combine($pk_key, $pk_val), true);
		$vals_list = $this->implode(', ', $vals, true);

		$this->query = 'UPDATE '.$this->table.' SET '.$vals_list.' WHERE '.$pks_list;
		$this->exec();

		if (method_exists($inst, 'postSave'))
			$inst->postSave();
		return $this;
	}

	public function insert() {
		if (method_exists($inst, 'preInsert'))
			$inst->preInsert();

		$vals = $this->inst->toArray();
		$pks = (array) $this->inst->getPK();
		foreach ($pks as $v)
			unset($vals[$v]);
		unset($pks, $v);

		$val_args = implode(', ', array_keys($vals));
		//FIXME: haxxor
		$val_list = implode(', ', array_fill(0, count($vals), '?'));

		$this->query = 'INSERT INTO '.$this->table.'('.$val_args.') VALUES ('.$val_list.')';
		$this->params($vals)->exec();

		if (method_exists($inst, 'postInsert'))
			$inst->postInsert();
		return $this;
	}

	public function remove() {
		if (method_exists($inst, 'preRemove'))
			$inst->preRemove();

		$pks = (array) $this->inst->getPK();

		$pks_list = $this->implode(' AND ', $pks, true);

		$this->query = 'DELETE FROM '.$this->table.' WHERE '.$pks_list;
		$this->exec();

		if (method_exists($inst, 'postRemove'))
			$inst->postRemove();
		$this->inst = NULL;
		return $this;
	}
}

/**
 * DBtabl - query builder/repository
 */
class DBtabl extends DB {
	protected $fields = '';

	protected $table = '';

	protected $set = '';
	protected $where = '';

	protected $values = '';

	function __construct($tab) {
		$this->table = $tab;
	}

	public function flush() {
		// code...
	}
}
