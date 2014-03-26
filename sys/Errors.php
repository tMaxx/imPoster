<?php ///revCMS \Error.php
namespace {
///Error class
class Error extends ErrorException {

	/**
	 * Return trimmed dirs in string
	 * @param $str
	 * @return trimmed ROOT
	 */
	public static function pathdiff($str) {
		return str_replace(ROOT, '', $str);
	}

	/**
	 * Prettify trace
	 * @param $trace
	 * @param $separator string
	 * @return string
	 */
	public static function prettyTrace($trace, $separator = '<br>') {
		$result = [];
		foreach ($trace as $i => $v) {
			$result[] = $i.'# ';

			if (isset($v['file']) && $v['file'])
				$result[] = self::pathdiff($v['file']).':'.$v['line'].' - ';
			else
				$result[] = '[internal call] ';

			if (isset($v['class']))
				$result[] = $v['class'].$v['type'];

			$result[] = $v['function'];

			if (isset($v['args']) && $v['args'])
				$result[] = htmlspecialchars(self::pathdiff(json_encode($v['args'], JSON_UNESCAPED_SLASHES|JSON_NUMERIC_CHECK)), ENT_COMPAT|ENT_HTML5);
			else
				$result[] = '()';

			$result[] = $separator;
		}
		return implode($result);
	}

	/**
	 * Custom all-error handler
	 * @param [standard params]
	 * @return varies
	 */
	public static function h($eno = NULL, $estr = NULL, $efile = NULL, $eline = NULL, $econtext = NULL) {
		static $constants;
		if (!isset($constants))
			$constants = get_defined_constants(1)['Core'];

		$trace = debug_backtrace();
		$result = array('<br><br>');

		if ((isset($eno, $estr, $efile)) || (!isset($eno) && (($e_last = error_get_last()) !== NULL))) { //error
			$eName = '?';

			if (isset($e_last['type']))
				$eno = $e_last['type'];

			foreach ($constants as $key => $value)
				if (substr($key, 0, 2) == 'E_' && $eno == $value) {
					$eName = $key;
					break;
				}

			if (isset($e_last['message'])) {
				$eName = '<b>FATAL</b>: '.$eName;
				$efile = $e_last['file'];
				$eline = $e_last['line'];
				$estr = nl2br(self::pathdiff($e_last['message']));
			}

			$result[] = '<big><b>Error</b></big>: '.$eName.': '.$estr;
			$result[] = '<br>'.self::pathdiff($efile).':'.$eline;
		} elseif (isset($eno)) { //exception handler
			$result[] = '<big><b>'.get_class($eno).'</b></big>: ';

			$result[] = (method_exists($eno, 'getExtMessage') ? $eno->getExtMessage() : $eno->getMessage());
			$result[] = '<br>'.self::pathdiff($eno->getFile()).':'.$eno->getLine();

			$trace = $eno->getTrace();
		}

		if ((isset($eno) && !($eno instanceof ErrorHTTP)) || isset($e_last)) {
			http_response_code(500);
		}

		if ($trace){
			$result[] = '<br>';
			$result[] = Error::prettyTrace($trace);
		}

		echo implode($result);
		return true;
	}
}

class ErrorCMS extends Error {}

class ErrorHTTP extends Error {
	public $httpcode;
	public $inmessage;
	public function __construct($msg = NULL, $code = NULL, $add = NULL) {
		$this->httpcode = $code;
		$this->inmessage = $msg;

		if ($code)
			http_response_code($code);

		$msg = 'HTTP '.(int)$code.': '.$msg;

		parent::__construct($msg);
	}

	public function getHttpCode() {
		return $this->httpcode;
	}
}

class Error404 extends ErrorHTTP {
	public function __construct($m = NULL, $add = NULL) {
		if (!$m) $m = 'Page not found';
		parent::__construct($m, 404, $add);
	}
}

class Error403 extends ErrorHTTP {
	public function __construct($m = NULL, $add = NULL) {
		if (!$m) $m = 'Forbidden';
		parent::__construct($m, 403, $add);
	}
}

class Error400 extends ErrorHTTP {
	public function __construct($m = NULL) {
		if (!$m) $m = 'Bad Request';
		parent::__construct($m, 400);
	}
}

class Error418 extends ErrorHTTP {
	public function __construct($m = NULL) {
		if (!$m) $m = "I\'m a teapot :D<br />(Some funny error occured, please return to <a href=\"/\">index</a> page)";
		parent::__construct($m, 400);
	}
}

class Error500 extends ErrorHTTP {
	public function __construct($m = NULL) {
		if (!$m) $m = 'Internal Server Error';
		parent::__construct($m, 500);
	}
}

class Error501 extends ErrorHTTP {
	public function __construct($m = NULL) {
		if (!$m) $m = 'Not (Yet...) Implemented';
		parent::__construct($m, 500);
	}
}

class Error503 extends ErrorHTTP {
	public function __construct($m = NULL) {
		if (!$m) $m = 'Site Overlo[ar]d';
		parent::__construct($m, 503);
	}
}

class Redirect extends ErrorHTTP {
	public function __construct($target = NULL) {
		if (!$target || !is_string($target))
			throw new ErrorCMS('Wrong redirect specified');
		HTTP::addHeader('Location: '.$target);
		die(); //die nicely
	}
}
}
namespace r3v {
class Error extends \Error {}
}
