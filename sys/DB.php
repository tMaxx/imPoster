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
	const MODE_TABLE = 3; //query builder
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
	///Query config
	protected $q = array();

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
		$this->q = array(
			'query' => '',
			'types' => '',
			'params' => array(),
		);
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
				$this->c['action'] = ''; //insert, update, delete
				$this->c['set'] = ''; //*only for update
				$this->c['fields'] = '*';
				$this->c['where'] = '';
				$this->c['order'] = ''; //order by
				$this->c['sort'] = ''; //sort asc/desc
				break;
			case self::MODE_INSTANCE:
				$this->c['inst'] = $var;
				$this->c['table'] = $var::table();
				break;
			case self::MODE_DIRECT:
				$this->q['query'] = $var;
				break;
		}
		$this->mode = $mode;
	}

	public function mode_get() {
		return $this->mode;
	}

	public function guess($name) {
		$this->mode_set($name);
		return $this;
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

	protected function retrieveState() {
		$this->param['affected_rows'] = self::$db->affected_rows;
		$this->param['field_count'] = self::$db->field_count;
		$this->param['insert_id'] = self::$db->insert_id;
	}


	public function exec() {
		$this->bindQuery();
		switch ($mode) {
			case self::MODE_TABLE:

				break;
			case self::MODE_INSTANCE:

				break;
			case self::MODE_DIRECT:
				if ($this->stmt)
					$this->result = $this->stmt->get_result();
				break;
		}

		$this->retrieveState();
		return $this;
	}

	protected function hlp_paramKV($arr, $glue = ', ') {
		$r = '';
		foreach ($arr as $k => $_) {
			$r .= $k . '=?';
		}
	}

	public function save() {
		if ($this->mode == self::MODE_INSTANCE) {
			$obj = &$this->c['inst'];
			$table = $this->c['table'];
			$data = $obj->toArray();

			if ($obj->getId()) {
				$query = 'UPDATE '.$table.' SET ';

				DB::update($table, $obj->getId(), $s);
			} else
				DB::insert($table, $s);

			$this->retrieveState();
		}
		return $this;
	}

	public function param($type, $val = NULL) {
		$this->bindQuery();
		if ($val === NULL) {
			$val = $type;
			$type = gettype($type)[0];
		}
		switch ($type[0]) {
			case 'b':
				$val = (bool) $val;
				break;
			case 'i':
				$val = (int) $val;
				break;
			case 'd':
				$val = (double) $val;
				break;
			case 'f':
				$val = (float) $val;
				break;
			case 'n':
			case 'N':
				$val = null;
			case 'c':
				$val = (string) $val[0];
			case 's':
			default:
				$val = self::$db->escape_string($val);
				break;
		}

		$this->stmt->bind_param($type[0], $val);
		return $this;
	}

	/**
	 * Perform a query, w. binding if necessary
	 * @param $tp types to bind
	 * @param $val values to bind
	 * @return bool|mysqli_result
	 */
	public function params($tps = '', array $val = array()) {
		if (strlen($tps) != ($c = count($val)))
			throw new Exception('DB: Number of types != number of values!');

		if (!($this->stmt = self::$db->prepare($q)))
			throw new Exception('DB: Error while preparing query');

		if ($c != $this->stmt->param_count)
			throw new Exception('DB: Number query params != number of values');

		for ($i = 0; $i < $c; $i++)
			$this->param($tps[$i], $val[$i]);

		//exec
		if (!($this->stmt->execute()))
			throw new Exception('DB: Error while executing query');

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
			while ($tmp = $this->result->fetch_assoc())
				$r[] = $tmp;

		return $r;
	}

	public function object($type = null) {
		if ($type === null)
			$type = $this->c['table'];
		$r = null;
		if ($tmp = $this->result->fetch_assoc())
			$r[] = new $type($tmp);

		return $r;
	}

	public function objects($type = null) {
		if ($type === null)
			$type = $this->c['table'];
		$r = array();
		while ($tmp = $this->result->fetch_assoc())
			$r[] = new $type($tmp);

		return $r;
	}

	/**
	 * Insert data into table
	 * @param $table name
	 * @param $data key: field name, value: field value
	 */
	public function insert($table, $data) {
		if ($this->mode == self::MODE_TABLE) {

		}
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
