<?php ///r3v engine \r3v\DB\Result
namespace r3v\DB;

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
	function __construct(&$statement, &$meta) {
		$this->stmt = &$statement;
		if ($meta) {
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

	///Free the statement
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
