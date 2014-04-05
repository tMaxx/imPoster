<?php ///r3v engine \r3v\DB\Error
namespace r3v\DB;

class Error extends \r3v\Error {
	protected $inst = NULL;

	public function __construct($msg = NULL, $inst = NULL) {
		if (is_object($inst))
			$this->inst = $inst;
		parent::__construct($msg);
	}

	public function getExtMessage() {
		$r = parent::getMessage();
		$s = Base::getErrors();
		if (!$s && isset($this->stmt))
			$s = $this->getStmtErrors();
		if ($s)
			$r .= ', details: "' . $s . '"';
		return $r;
	}
}
