<?php ///revCMS /sys/DB.php
/**
 * DB - Database support class
 */
class DB extends _Locks {
	//db object
	private static $db = NULL;
	///instance working mode
	const MODE_NONE = 0;
	const MODE_DIRECT = 1;
	const MODE_INSTANCE = 2;
	const MODE_TABLE = 3;
	////direct query, instance Model manipulation, repository mode
	protected $mode = self::MODE_NONE;
	///compiled query
	protected $query = '';
	///statement
	protected $stmt = null;
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

	protected function mode_clear() {
		if ($this->stmt) {
			$this->stmt->close();
			$this->stmt = null;
		}
		if ($this->result) {
			$this->result->free();
			$this->result = null;
		}
		$this->param = array();
		$this->query = '';
		$this->c = array();
		$this->mode = 0;
	}

	protected function mode_set($var) {
		$this->mode_clear();

		if ($var instanceof Model)
			$mode = self::MODE_INSTANCE;
		elseif (is_string($var) && CMS::appClassExists($var))
			$mode = self::MODE_TABLE;
		else
			$mode = self::MODE_DIRECT;

		//set up
		$this->c['types'] = '';
		$this->c['params'] = array();

		switch ($mode) {
			case self::MODE_TABLE:
				$this->query = '';
				$this->c['table'] = $var;
				$this->c['fields'] = '*';
				$this->c['where'] = '';
				$this->c['order'] = '';
				$this->c['sort'] = '';
				break;
			case self::MODE_INSTANCE:
				$this->query = '';
				$this->c['action'] = '';
				$this->c['instance'] = $var;
				break;
			case self::MODE_DIRECT:
				$this->query = $var;
				break;
		}
		$this->mode = $mode;
		return $this;
	}

	public function mode_get() {
		return $this->mode;
	}

	function __construct($something = null) {
		if ($something !== null)
			$this->mode_set($something);
		pre_dump($this);
	}

	function __destruct() {
		$this->mode_clear();
	}

	public function guess($name) {
		return $this->mode_set($name);
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

	//////////////////////////////////////////////QUERY HANDLING\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\

	private function implode(array $arr, $glue) {
		$r = '';

		if (isset($arr[0])) { //assume all keys are numeric
			// $r .= implode(
		}
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
		$this->c['types'] .= $type;
		$this->c['params'][] = $val;
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

	///Get all pieces together
	protected function compileQuery() {
		switch ($this->mode) {
			case self::MODE_INSTANCE: {
				if ($this->c['instance']->getId()) { //save

				} else { //insert

				}
				break;
			}
			case self::MODE_TABLE: {

				break;
			}
		}
		return $this;
	}

	///Prepare all queries
	protected function prepareQuery() {
		if (!$this->stmnt) {
			$types = $this->c['types'];
			$values = $this->c['params'];
			if (strlen($types) != ($c = count($values)))
				throw new Exception('DB: Number of types != number of values!');

			$this->compileQuery();

			if (!($this->stmnt = self::$db->prepare($this->query)))
				throw new Exception('DB: Error while preparing query');

			if ($c != $this->stmnt->param_count)
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

				$this->stmnt->bind_param($types[0], $val[$i]);
			}
		}
		return $this;
	}

	///Compile and execute query
	public function exec() {
		if (!$this->result) {
			if (!($this->stmnt->execute()))
				throw new Exception('DB: Error while executing query');

			$this->result = $this->stmnt->get_result();
			$this->retrieveState();
		}
		return $this;
	}

	/**
	 * Insert data into table
	 * @param $data key: field name, value: field value
	 */
	public function save() {

	}

	//////////////////////////////////////////////RESULTS HANDLING\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\

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
		if (method_exists('mysqli_result', 'fetch_all'))
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
