<?php ///rev engine \rev\DB\Error
namespace rev\DB;

class Error extends \rev\Error {
	public $inst = NULL;

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
