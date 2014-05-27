<?php ///rev engine \rev\CRUD\CRUD
namespace rev\CRUD;
use \rev\Error, \rev\Error404;

/** CRUD - basic Create/Read/Update/Delete */
class CRUD {
	const MODE_NONE = 'none';
	const MODE_USER = 'user';
	const MODE_ADMIN = 'admin';
	protected $name;
	protected $def = [];
	protected $object = null;
	protected $cache = [];

	public function __construct($path, $mode = self::MODE_NONE) {
		$name = $path;
		$path = \rev\View::getCurrentBasepath() . 'crud/' . $path . '.json';

		$def = \rev\File::jsonFromFile($path);
		if ($def === false)
			throw new Error("CRUD: invalid path for definition {$path}");

		if (!(
				($mode == self::MODE_USER || $mode == self::MODE_ADMIN)
				&&
				isset($def['mode_'.$mode])
			))
			throw new Error("CRUD: $mode mode chosen but not defined");
		elseif ($mode != self::MODE_NONE)
			$def = array_replace($def, $def['mode_'.$mode]);

		$def['mode'] = $mode;
		$this->name = $def['name'] = "crud:$mode:$name"; unset($name);

		$this->object = $def;
		unset($def['fields']);
		$this->def = $def;
	}

	/** Items on page count */
	public function getIOPC() {
		return $this->def['items_on_page'];
	}

	protected function checkPageNumeric($pnum) {
		return (((!ctype_digit((string)$pnum))) || ((int)$pnum) < 0);
	}

	protected function getMaxPageNumber() {
		if (!array_key_exists('max_page', $this->cache))
			$this->cache['max_page'] = (int)floor($this->_getCount() / $this->def['items_on_page']);
		return $this->cache['max_page'];
	}

	protected function _getCount() {
		if (array_key_exists('count', $this->cache))
			return $this->cache['count'];
		return $this->cache['count'] = \rev\DB\Q($this->def['table'])->select('COUNT(id)')->where($this->def['select_where'])->val();
	}

	/** Render page navigation */
	public function navigation($pnum = null) {
		$ret = [];
		$count = $this->getMaxPageNumber();
		if ($pnum === null)
			$pnum = $count;
		elseif ($this->checkPageNumeric($pnum))
			throw new Error('CRUD: Page must be an integer');

		if ($count > $pnum)
			$ret[] = ['<<<', $pnum];
		else
			$ret[] = '<<<';

		for ($i = $count; $i >= 0; $i--)
			if ($i == $pnum)
				$ret[] = $i;
			else
				$ret[] = [$i, $i];


		if (0 < $pnum)
			$ret[] = ['>>>', $pnum];
		else
			$ret[] = '>>>';
		return $ret;
	}

	/** Return array of page short entries */
	public function page($pnum = null) {
		if ($pnum === null)
			$pnum = $this->getMaxPageNumber();
		elseif ($this->checkPageNumeric($pnum))
			throw new Error('CRUD: Page number must be an integer');

		$count = $this->_getCount();
		if (!$count || $count === '0')
			return 'first!';
		if ($pnum > $this->getMaxPageNumber())
			throw new Error404();

		// short overview:
		// - page 0 is last
		// - page has entries from $count to max($count-$perpg, 0)
		$ret = \rev\DB\Q($this->def['table']);
		$ret->select($this->def['select_short'])->where($this->def['select_where']);

		if (!empty($this->def['select_order_by']))
			$ret->endparams('ORDER BY '.$this->def['select_order_by']);

		$per = $this->def['items_on_page'];
		$start = $count - (($pnum + 1) * $per);
		$pnum = max($start, 0);
		if ($start < 0 && ((-$start) <= (4*$per/5))) //less than i_o_p
			$per -= $start;

		$ret->endparams('LIMIT ?,?')->params('ii', [$pnum, $per]);
		return $ret->rows();
	}

	/** Return internal property value */
	public function __get($name) {
		if ($name == 'obj' || $name == 'object' || $name == 'single') {
			//lazy loader
			if (is_array($this->object))
				$this->object = new Object($this->object);
			return $this->object;
		}
	}
}
