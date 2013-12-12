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
		switch ($mode) {
			case self::MODE_TABLE:
				$this->c['table'] = $var;
				$this->c['fields'] = '*';
				$this->c['where'] = '';
				$this->c['order'] = '';
				$this->c['sort'] = '';
				break;
			case self::MODE_INSTANCE:
				$this->c['inst'] = $var;
				$this->c['table'] = $var::table();
				break;
			case self::MODE_DIRECT:
				$this->c['query'] = $var;
				$this->c['types'] = '';
				$this->c['params'] = array();
				break;
		}
		$this->mode = $mode;
	}

	public function mode_get() {
		return $this->mode;
	}

	function __construct($query = null) {
		$this->c['query'] = $query;
		$this->c['types'] = null;
		$this->c['params'] = null;
		$this->table = NULL;
		pre_dump($this);
	}

	function __destruct() {
		$this->mode_clear();
	}

	public function guess($name) {
		$this->mode_set($name);
		return $this;
	}

	public function instance($inst) {
		$this->mode_set(self::MODE_INSTANCE);
		return $this;
	}


	protected function retrieveState() {
		$this->param['affected_rows'] = self::$db->affected_rows;
		$this->param['field_count'] = self::$db->field_count;
		$this->param['insert_id'] = self::$db->insert_id;
	}


	public function exec() {
		$this->retrieveState();
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

	public function param($type, $val = NULL) {
		if($val === NULL)
			$val = $type;
		switch ($type[0]) {
			case 'i':
				$val = (int) $val;
				break;
			case 'd':
				$val = (double) $val;
				break;
			case 'f':
				$val = (float) $val;
				break;
			case 'c':
				$val = (string) $val[0];
			case 's':
			default:
				$val = self::$db->escape_string($val);
				break;
		}

		$this->stmnt->bind_param($type[0], $val);
		return $this;
	}

	/**
	 * Perform a query, w. binding if necessary
	 * @param $tp types to bind
	 * @param $val values to bind
	 * @return bool|mysqli_result
	 */
	public function params($tps = '', array $val = array()) {
		if (!tps || !val)
			$this->result = $db->query($q);
		else {
			if (strlen($tps) != ($c = count($val)))
				throw new Exception('DB: Number of types != number of values!');

			if (!($this->stmnt = self::$db->prepare($q)))
				throw new Exception('DB: Error while preparing query');

			if ($c != $this->stmnt->param_count)
				throw new Exception('DB: Number query params != number of values');

			for ($i = 0; $i < $c; $i++)
				$this->param($tps[$i], $val[$i]);

			//exec
			if (!($this->stmnt->execute()))
				throw new Exception('DB: Error while executing query');

			$this->result = $this->stmnt->get_result();
		}
		return $this;
	}

	/**
	 * Fetch a single row from db
	 */
	public function row() {
		return $this->result->fetch_assoc();
	}

	/**
	 * Fetch a set of rows from db
	 */
	public function rows() {
		$r = array();
		if (method_exists('mysqli_result', 'fetch_all'))
			$r = $this->result->fetch_all(MYSQLI_ASSOC);
		else
			while($tmp = $this->result->fetch_assoc())
				$r[] = $tmp;

		return $r;
	}

	/**
	 * Insert data into table
	 * @param $table name
	 * @param $data key: field name, value: field value
	 */
	public function insert($table, $data) {
		
	}

	/**
	 * Update data in table
	 * @param $table name
	 * @param $where key: field name, value: field value
	 * @param $set key: field name, value: field value
	 */
	public function update($table, $where, $set) {
		
	}

}
