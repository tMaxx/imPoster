<?php ///rev engine \rev\CRUD\CRUD
namespace rev\CRUD;
use \rev\Error, \rev\DB\Q;

/** CRUD - basic Create/Read/Update/Delete */
class CRUD {
	protected $name;
	protected $def = [];
	protected $object = null;
	protected $tmp = [];

	public function __construct($path) {
		$name = $path;
		$path = \rev\View::getCurrentBasepath() . 'crud/' . $path . '.json';

		$def = \rev\File::jsonFromFile($path);
		if ($def === false)
			throw new Error("CRUD: invalid path for definition {$path}");

		$this->name = $def['name'] = 'crud:'.$name; unset($name);

		$this->object = $def;
		unset($def['fields']);
		$this->def = $def;
	}

	/** Lazy loader for single object management */
	public function object() {
		if (is_array($this->object))
			$this->object = new Object($this->object);
		return $this->object;
	}

	public function navigation() {
		// code...
	}

	/** Return array of page short entries */
	public function page($pnum, $cond = null) {
		if (((!ctype_digit((string)$pnum))) || ((int)$pnum) < 0)
			throw new Error('CRUD: Wrong input format');

		$cond = empty($this->def['where']) ? $cond : $this->def['where'];
		$cond = $cond ?: '';
		$count = Q($this->table)->select('COUNT(id)')->where($cond)->val();
		if (!$count || $count === '0')
			return 'first!';

		$this->tmp['count'] = $count;

		$per = $this->def['items_on_page'];

		if (($pnum * $per) > $count)
			throw new Error404();

		// short overview:
		// - page 0 is last
		// - page has entries from $count to max($count-5, 0)
		$ret = Q($this->table);
		$ret->select($this->def['select_short'])->where($cond);

		if ($this->def['select_order_by'])
			$ret->endparams('ORDER BY '.$this->def['select_order_by']);

		$pnum = $count - (($pnum + 1) * $per);

		$ret->endparams('LIMIT ?,?')->params('ii', [$pnum, $per]);
		return $ret->rows();
	}
}
