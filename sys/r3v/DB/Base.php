<?php ///r3v engine \r3v\DB\Base
namespace r3v\DB;

/**
 * Base - Database support class
 */
class Base {
	//db object
	protected static $db = NULL;
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
	public static function go() {
		if (!class_exists('\\mysqli', false))
			return;
		if (self::$db)
			return;

		$con = \r3v\Conf::db();

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

	/**
	 * Return number of rows from last query
	 * @return int
	 */
	public function getNumberOfRows() {
		return $this->db_status['num_rows'];
	}

	///Get DB state after action
	protected function retrieveState() {
		if (isset($this->stmt)) {
			$num = $this->stmt->num_rows;
			$aff = $this->stmt->affected_rows;
			$fc = $this->stmt->field_count;
		} else {
			if ($this->query_result instanceof \mysqli_result)
				$num = $this->query_result->num_rows;
			else
				$num = false;
			$aff = self::$db->affected_rows;
			$fc = self::$db->field_count;
		}

		$this->db_status['num_rows'] = $num;
		$this->db_status['affected_rows'] = $aff;
		$this->db_status['field_count'] = $fc;
		$this->db_status['insert_id'] = self::$db->insert_id;
		return $this;
	}

	public static function getErrors() {
		if (!isset(self::$db->error))
			return '';
		$r = self::$db->error;
		if (!$r && self::$db->error_list)
			$r = print_r(self::$db->error_list, true);
		return $r;
	}

	public function getStmtErrors() {
		if (isset($this->stmt)) {
			$r = $this->stmt->error;
			if (!$r && $this->stmt->error_list)
				$r = print_r($this->stmt->error_list, true);
			return $r;
		}
		return '';
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
				if ($v === NULL && !$setter && !$iglue)
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
			for ($i = 0, $c = count($values); $i < $c; $i++)
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
				throw new Error('Number of types != number of values!', $this);

			if (($this->stmt = self::$db->prepare($this->query)) === false)
				throw new Error('Mishap while preparing query', $this);

			if ($c != $this->stmt->param_count)
				throw new Error('Number query params != number of values', $this);

			$params = array();

			for ($i = 0; $i < $c; $i++) {
				switch ($types[$i]) {
					case 'b': //blob - noop || boolean
						if (is_bool($values[$i]))
							$types[$i] = 'i';
						else
							throw new Error('Param type blob not yet supported');
						break;
					case 'n': //null
						$values[$i] = NULL;
						$types[$i] = 'i';
						break;
					case 'i':
					case 'd':
					case 's':
						break;
					default:
						$types[$i] = 's';
						break;
				}
				if ($values[$i] !== NULL)
					switch ($types[$i]) {
						case 'i':
							if (!is_numeric($values[$i]))
								$values[$i] = (int) $values[$i];
							break;
						case 'd':
							if (!is_numeric($values[$i]))
								$values[$i] = (double) $values[$i];
							break;
						case 's':
							$values[$i] = (string) $values[$i];
							break;
					}
				$params[] = &$values[$i];
			}

			array_unshift($params, $types);
			if (!call_user_func_array(array($this->stmt, 'bind_param'), $params))
				throw new Error('Could not bind query params', $this);
		}
		return TRUE;
	}

	///Compile and execute query
	public function exec() {
		if (!$this->query_result) {
			if ($this->bindquery()) {
				if (!($this->query_result = $this->stmt->execute()))
					throw new Error('Query execution unsuccessful', $this);

				if ($meta = $this->stmt->result_metadata()) {//has result set
					if (!$this->stmt->store_result())
						throw new Error('Statement\'s store_result failed');
					$this->query_result = new Result($this->stmt, $meta);
				}
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
		if (!is_object($this->exec()->query_result))
			return null;
		return $this->query_result->fetch_assoc();
	}

	///Fetch a set of rows from db
	public function rows() {
		if (!is_object($this->exec()->query_result))
			return array();

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

		return (($r = $this->row()) ? new $type($r) : $r);
	}

	public function objs($type = NULL) {
		if (!$type && $this instanceof Table)
			$type = $this->table;

		$r = $this->rows();
		foreach ($r as $k => $v)
			$r[$k] = new $type($v);

		return $r;
	}

	public function num() {
		if (!is_object($this->exec()->query_result))
			return null;

		return $this->query_result->fetch_row();
	}

	public function nums() {
		if (!is_object($this->exec()->query_result))
			return array();

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

	public function bool() {
		$this->exec();
		return !!($this->getAffectedRows() || $this->getNumberOfRows());
	}

	public function val() {
		if (!is_object($this->exec()->query_result))
			return null;
		$v = $this->query_result->fetch_row();
		return $v[0];
	}

	public function vals() {
		$q = $this->nums();
		$r = array();
		foreach ($q as $v)
			$r[] = $v[0];
		return $r;
	}
}

\r3v\DB\Base::go();
\r3v\Mod::registerUnload(['\\r3v\\DB\\Base::end']);
