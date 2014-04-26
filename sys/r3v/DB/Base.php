<?php ///rev engine \r3v\DB\Base
namespace r3v\DB;
use \PDO, \PDOStatement;

/**
 * Base - Database support class
 */
class Base {
	/** db object */
	protected static $db = null;
	/** compiled query */
	protected $query = '';

	/** statement & params */
	protected $stmt = null;
	protected $stmt_types = '';
	protected $stmt_values = array();

	/** db state after last query */
	protected $db_status = array();

	/**
	 * Prepare the DB and connect to it
	 * @param $con connection options
	 */
	public static function go() {
		if (self::$db)
			return;
		if (!class_exists('\\PDO', false))
			throw new Error('Could not find PDO extension. Aborting.');

		$con = \r3v\Conf::db();

		$dsn = 'mysql:';
		if (!empty($con['socket']))
			$dsn .= 'unix_socket='.$con['socket'].';';
		elseif (!empty($con['host']) && !empty($con['port']))
			$dsn .= 'host='.$con['host'].';port='.$con['port'].';';
		$dsn .= 'dbname='.$con['dbname'].';charset=utf8';

		try {
			self::$db = new \PDO($dsn, $con['user'], $con['pass']);
		}
		catch (\PDOException $e) {
			throw new Error('Could not connect to db, details: '.$e->getMessage());
		}

		// set error reporting mode
		self::$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}

	/** End execution, close everything */
	public static function end() {
		if (isset(self::$db))
			self::$db = null;
	}

	/** Construct, with query as param */
	function __construct($var) {
		$this->query = $var;
	}

	/** Destruct, closing statement cursor */
	function __destruct() {
		if ($this->stmt)
			$this->stmt->closeCursor();
		$this->stmt = null;
	}

	/** Allow access for db and stmt */
	public function __get($v) {
		switch ($v) {
			case 'db':
				return self::${$v};
				break;
			case 'stmt':
				return $this->{$v};
				break;
		}
		return null;
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
		return $this->db_status['field_count'];
	}

	/**
	 * Return number of rows from last query
	 * @return int
	 */
	public function getNumberOfRows() {
		return $this->db_status['num_rows'];
	}

	/** Get DB state after action */
	protected function retrieveState() {
		$num = $fc = null;
		if (isset($this->stmt)) {
			$num = $this->stmt->rowCount();
			$fc = $this->stmt->columnCount();
		}

		$this->db_status['num_rows'] = $num;
		$this->db_status['field_count'] = $fc;
		$this->db_status['insert_id'] = self::$db->lastInsertId();
		return $this;
	}

	/** Return db errors as string */
	public static function getErrors() {
		if (!self::$db)
			return '';
		$ec = self::$db->errorCode();
		if (!$ec || $ec === '00000')
			return '';

		if ($ei = self::$db->errorInfo())
			$ec .= ' '.print_r($ei, true);
		return $ec;
	}

	/** Return string of statement errors */
	public function getStmtErrors() {
		if (!isset($this->stmt))
			return '';

		$ec = $this->stmt->errorCode();
		if (!$ec || $ec === '00000')
			return '';

		if ($ei = $this->stmt->errorInfo())
			$ec .= ' '.print_r($ei, true);
		return $ec;
	}

	// ==================================================================
	//
	// Query builders, handlers, internals
	//
	// ------------------------------------------------------------------

	protected function implode($glue, array $arr, $parametrize = false, $setter = false, $iglue = '') {
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
				if ($v === null && !$setter && !$iglue)
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
	public function param($type, $val = null) {
		if ($val === null) {
			$val = $type;
			$type = strtolower(gettype($val)[0]);
		}
		$this->stmt_types .= $type;
		$this->stmt_values[] = $val;
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

	/** Prepare all queries */
	protected function bindquery() {
		if (!$this->stmt) {
			if (method_exists($this, 'createquery'))
				$this->createquery();

			if (!$this->stmt_types || !$this->stmt_values)
				return false;

			if (strlen($this->stmt_types) != ($c = count($this->stmt_values)))
				throw new Error('Number of types != number of values!', $this);

			if (($this->stmt = self::$db->prepare($this->query)) === false)
				throw new Error('Mishap while preparing query', $this);

			for ($i = 0; $i < $c; $i++) {
				switch (strtolower($this->stmt_types[$i])) {
					case 'b': //blob ^ boolean
						$ptype = (is_bool($this->stmt_values[$i]) ? PDO::PARAM_BOOL : PDO::PARAM_LDO);
						break;
					case 'n': //null
						$this->stmt_values[$i] = null;
						$ptype = PDO::PARAM_NULL;
						break;
					case 'i':
						$ptype = PDO::PARAM_INT;
						break;
					case 'd': //for doubles PARAM_STR is assumed anyway
					case 's':
					default:
						$ptype = PDO::PARAM_STR;
						break;
				}

				if (ctype_upper($this->stmt_types[$i]))
					$ptype |= PDO::PARAM_INPUT_OUTPUT;

				$this->stmt->bindParam($i+1, $this->stmt_values[$i], $ptype);
			}
		}
		return true;
	}

	/** Compile and execute query */
	public function exec() {
		if (!$this->stmt) {
			if ($this->bindquery()) {
				if (!$this->stmt->execute())
					throw new Error('Query execution unsuccessful', $this);
			} else {
				if (!$this->query)
					throw new Error('Query is empty!');

				if (($this->stmt = self::$db->query($this->query)) === false)
					throw new Error('Error when executing query: "'.
						(strlen($this->query) > 200 ? mb_substr($this->query, 0, 200).'(...)' : $this->query).'"');
			}
			$this->retrieveState();
		}
		return $this;
	}

	/** Closes statement cursor (no more results are required) */
	public function close() {
		if ($this->stmt)
			$this->stmt->closeCursor();
		return $this;
	}

	// ==================================================================
	//
	// Result handling functions
	//
	// ------------------------------------------------------------------

	/** Fetch a single row from db */
	public function row() {
		if (!($r = $this->exec()->stmt->fetch(PDO::FETCH_ASSOC)))
			$this->stmt->closeCursor();
		return $r;
	}

	/** Fetch a set of rows from db */
	public function rows() {
		$r = $this->exec()->stmt->fetchAll(PDO::FETCH_ASSOC);
		$this->stmt->closeCursor();

		return $r ?: [];
	}

	/** Return result as array, with keys of given column name */
	public function rowsBy($key) {
		$q = $this->rows();
		$r = array();

		foreach ($q as $v)
			$r[$v[$key]] = $v;

		return $r;
	}

	/** Return result as object by param */
	public function obj($type = null) {
		if (!$type && $this instanceof Table)
			$type = $this->table;

		return (($r = $this->row()) ? new $type($r) : $r);
	}

	/** Return result as array of objects by param */
	public function objs($type = null) {
		if (!$type && $this instanceof Table)
			$type = $this->table;

		$r = $this->rows();
		foreach ($r as $k => $v)
			$r[$k] = new $type($v);

		return $r;
	}

	/** Return result as numeric array */
	public function num() {
		if (!($r = $this->exec()->stmt->fetch(PDO::FETCH_NUM)))
			$this->stmt->closeCursor();
		return $r;
	}

	/** Return result as array of numeric arrays */
	public function nums() {
		$r = $this->exec()->stmt->fetchAll(PDO::FETCH_NUM);
		$this->stmt->closeCursor();

		return $r ?: [];
	}

	/** Return result as pairs 1st col => 2nd col */
	public function pairs() {
		$q = $this->nums();
		$r = array();
		foreach ($q as $v)
			$r[$v[0]] = $v[1];
		return $r;
	}

	/** Return if query changed any rows in db */
	public function bool() {
		$this->exec();
		return !!$this->getNumberOfRows();
	}

	/** Return single, 1st col value from result */
	public function val() {
		$v = $this->num();
		if (($v = $this->num()) === null)
			return null;
		return $v[0];
	}

	/** Return array of 1st col values from result */
	public function vals() {
		$q = $this->nums();
		$r = array();
		foreach ($q as $v)
			$r[] = $v[0];
		return $r;
	}
}

\r3v\DB\Base::go();
//\r3v\Mod::registerUnload(['\\r3v\\DB\\Base::end']);
