<?php ///revCMS /sys/DB.php
/**
 * DB - Database support class
 */
class DB extends _Locks {
	//db object
	private static $db = NULL;
	////direct query, instance Model manipulation, repository mode
	protected $mode = self::MODE_NONE;
	///compiled query
	protected $query = '';
	///statement
	protected $stmt = null;
	protected $stmt_types = '';
	protected $stmt_param = array();
	///last query's result
	protected $result = null;
	///db state after last query
	protected $param = array();
	///Config
	protected $c = array();

	/**
	 * Prepare the DB and connect to it
	 * @param $con connection options
	 */
	public static function go($con) {
		if (self::lock())
			return;

		if (!isset($con['host']) || !isset($con['user']) || !isset($con['pass']) || !isset($con['dbname']))
			throw new Exception('DB: Not sufficient connection parameters!');

		self::$db = new mysqli($con['host'], $con['user'], $con['pass'], $con['dbname']);

		if (self::$db->connect_error)
			throw new Exception('DB: Error while connecting: '.self::$db->connect_errno);

		if(!self::$db->set_charset("utf8"))
			throw new Exception('DB: Error while setting charset');
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
		if ($var instanceof Model)
			return new DBinst($var);
		elseif (is_string($var)) {
			if (count(explode(' ', $var)) > 1)
				return new DBtabl($var);
			$this->query = $var;
		} else
			throw new ErrorDB('Unsupported $var type');
	}

	function __destruct() {
		if ($this->stmt) {
			$this->stmt->close();
			$this->stmt = null;
		}
		if ($this->result) {
			$this->result->free();
			$this->result = null;
		}
	}

	/**
	 * Return no of rows affected by last query
	 * @return int
	 */
	public function getAffectedRows() {
		return $this->param['affected_rows'];
	}

	/**
	 * Return id of last insert
	 * @return int
	 */
	public function getInsertID() {
		return $this->param['insert_id'];
	}

	/**
	 * Return field count of last query
	 * @return int
	 */
	public function getFieldCount() {
		return $this->param['field_counts'];
	}

	///Get DB state after action
	protected function retrieveState() {
		$this->param['affected_rows'] = self::$db->affected_rows;
		$this->param['field_count'] = self::$db->field_count;
		$this->param['insert_id'] = self::$db->insert_id;
		return $this;
	}

	// ==================================================================
	//
	// Query builders, handlers, internals
	//
	// ------------------------------------------------------------------

	private function implode(array $arr, $glue) {
		$r = '';

		foreach ($arr as $k => $v)
			if (is_numeric($k))
				$r .= $v . $glue;
			else
				$r .= $k . '=' . $v . $glue;
		
		return $r;
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
	protected function prepareQuery() {
		if (!$this->stmt) {
			$types = $this->stmt_types;
			$values = $this->stmt_param;
			if (strlen($types) != ($c = count($values)))
				throw new Exception('DB: Number of types != number of values!');

			if (function_exists(array($this, 'compileQuery')))
				$this->compileQuery();

			if (!$this->stmt_types)
				return FALSE;

			if (!($this->stmt = self::$db->prepare($this->query))
				throw new Exception('DB: Error while preparing query');

			if ($c != $this->stmt->param_count)
				throw new Exception('DB: Number query params != number of values');

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
		if (!$this->result) {
			if ($this->prepareQuery()) {
				if (!($this->stmt->execute()))
					throw new Exception('DB: Error while executing query');
				$this->result = new DBresult($this->stmt);
			} else {
				$this->stmt = NULL;
				$this->result = self::$db->query($this->query);
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
		return $this->exec()->result->fetch_assoc();
	}

	///Fetch a set of rows from db
	public function rows() {
		$this->exec();
		$r = array();
		if (method_exists('mysqli_result', 'fetch_all'))
			$r = $this->result->fetch_all(MYSQLI_ASSOC);
		else
			while($tmp = $this->result->fetch_assoc())
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
		if ($this->mode == self::MODE_TABLE)
			$type = $this->c['table'];

		return new $type($this->row());
	}

	public function objs($type = NULL) {
		if ($this->mode == self::MODE_TABLE)
			$type = $this->c['table'];
		
		$r = $this->rows();
		foreach ($r as &$v)
			$v = new $type($v);
		reset($r);

		return $r;
	}

	public function num() {
		return $this->exec()->result->fetch_row();
	}

	public function nums() {
		$this->exec();
		$r = array();
		if (method_exists($this->result, 'fetch_all'))
			$r = $this->result->fetch_all(MYSQLI_NUM);
		else
			while($tmp = $this->result->fetch_row())
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
		if (method_exists('mysqli_stmt', 'get_result'))
			return $statement->get_result();
		$this->stmt = $statement;
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
		$this->stmt = NULL;
	}

	public function free() {
		// noop
	}

	protected function next($type) {
		if ($this->last != $type) {
			$this->result_fields = array();
			if ($type == MYSQLI_ASSOC)
				foreach ($this->fields_assoc as $k => $_)
					$this->result_fields[] = &$this->fields_assoc[$k];
			else
				foreach ($this->fields_num as $k => $_)
					$this->result_fields[] = &$this->fields_num[$k];

			$this->last = $type;
			if (!call_user_func_array(array($this->stmt, 'bind_result'), $this->result_fields))
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
		if ($q === NULL)
			return NULL;

		return $r;
	}
}


/**
 * DBinst - model instance handler
 */
class DBinst extends DB {
	protected $inst = NULL;

	function __construct($inst) {
		$this->inst = $inst;
	}

	public function save() {
		if (!$inst->getId())
			return $this->insert();

		if (function_exists(array($inst, 'preSave')))
			$inst->preSave();

		//TODO

		if (function_exists(array($inst, 'postSave')))
			$inst->postSave();
	}

	public function insert() {
		if (function_exists(array($inst, 'preInsert')))
			$inst->preInsert();

		//TODO

		if (function_exists(array($inst, 'postInsert')))
			$inst->postInsert();
	}

	public function remove() {
		if (function_exists(array($inst, 'preRemove')))
			$inst->preRemove();

		//TODO

		if (function_exists(array($inst, 'postRemove')))
			$inst->postRemove();
		$this->inst = NULL;
	}
}

/**
 * DBqb - query builder
 */
class DBqb extends DB {
	protected $table = '';

	function __construct($tab) {
		$this->table = $tab;
/*		$this->query = '';
		$this->c['table'] = $var;
		$this->c['fields'] = '*';
		$this->c['where'] = '';
		$this->c['order'] = '';
		$this->c['sort'] = '';*/
	}

}
