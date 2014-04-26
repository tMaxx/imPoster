<?php ///rev engine \rev\Error
namespace rev;
///Error class
class Error extends \ErrorException {

	/**
	 * Return trimmed dirs in string
	 * @param $str
	 * @return trimmed ROOT
	 */
	public static function pathdiff($str) {
		return str_replace(ROOT, '', $str);
	}

	public static function friendlyErrorType($type) {
		switch($type) {
			case E_ERROR:					return 'E_ERROR';
			case E_WARNING:				return 'E_WARNING';
			case E_PARSE:					return 'E_PARSE';
			case E_NOTICE:					return 'E_NOTICE';
			case E_CORE_ERROR:			return 'E_CORE_ERROR';
			case E_CORE_WARNING:			return 'E_CORE_WARNING';
			case E_CORE_ERROR:			return 'E_COMPILE_ERROR';
			case E_CORE_WARNING:			return 'E_COMPILE_WARNING';
			case E_USER_ERROR:			return 'E_USER_ERROR';
			case E_USER_WARNING:			return 'E_USER_WARNING';
			case E_USER_NOTICE:			return 'E_USER_NOTICE';
			case E_STRICT:					return 'E_STRICT';
			case E_RECOVERABLE_ERROR:	return 'E_RECOVERABLE_ERROR';
			case E_DEPRECATED:			return 'E_DEPRECATED';
			case E_USER_DEPRECATED:		return 'E_USER_DEPRECATED';
			default:							return '';
		}
	}

	public static function formatErrorLine($file, $line) {
		return '=> '.self::pathdiff($file).':'.$line;
	}

	/**
	 * Prettify trace
	 * @param $trace
	 * @param $args include arguments?
	 * @param $mode string: "<br>", "\n", "array"
	 * @return string
	 */
	public static function prettyTrace($trace, $args = false, $mode = NEWLINE) {
		$result = [];
		foreach ($trace as $i => $v) {
			$line = [];
			$line[] = $i.'# ';

			if (isset($v['file']) && $v['file'])
				$line[] = self::pathdiff($v['file']).':'.$v['line'].' - ';
			else
				$line[] = '[internal call] ';

			if (isset($v['class']))
				$line[] = $v['class'].$v['type'];

			$line[] = $v['function'];

			if ($args && isset($v['args']) && $v['args'])
				$line[] = htmlspecialchars(self::pathdiff(json_encode($v['args'], JSON_UNESCAPED_SLASHES|JSON_NUMERIC_CHECK)), ENT_COMPAT|ENT_HTML5);
			else
				$line[] = '()';

			$result[] = implode($line);
		}
		if ($mode == 'array')
			return $result;
		return implode($mode, $result);
	}

	/**
	 * Custom all-error handler
	 * @param [standard params]
	 * @return varies
	 */
	public static function h($eno = NULL, $estr = NULL, $efile = NULL, $eline = NULL, $econtext = NULL) {
		$trace = debug_backtrace();
		$result = [NEWLINE];

		if ((isset($eno, $estr, $efile)) || (!isset($eno) && (($e_last = error_get_last()) !== NULL))) { //error
			$eName = '?';

			if (isset($e_last['type']))
				$eno = $e_last['type'];

			if ($eName = self::friendlyErrorType($eno))
					array_shift($trace);

			if (isset($e_last['message'])) {
				$eName = '<b>FATAL</b> '.$eName;
				$efile = $e_last['file'];
				$eline = $e_last['line'];
				$estr = nl2br(self::pathdiff($e_last['message']));
			}

			$result[] = '<big><b>Error</b></big>  ['.$eName.']  '.$estr;
			$result[] = NEWLINE.self::formatErrorLine($efile, $eline);
		} elseif (isset($eno)) { //exception handler
			$result[] = '<big><b>'.get_class($eno).'</b></big>  ';

			$result[] = (method_exists($eno, 'getExtMessage') ? $eno->getExtMessage() : $eno->getMessage());
			$result[] = NEWLINE.self::formatErrorLine($eno->getFile(), $eno->getLine());

			$trace = $eno->getTrace();
		}

		if ((isset($eno) && !($eno instanceof ErrorHTTP)) || isset($e_last))
			http_response_code(500);

		if ($trace)
			$result[] = NEWLINE.Error::prettyTrace($trace);
		$result = implode($result);

		if (CLI)
			$result = strip_tags($result);

		echo $result;
		return true;
	}
}

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
		if (!$m) $m = "I'm a teapot :D".NEWLINE."(Some funny error occured, please return to <a href=\"/\">index</a> page)";
		parent::__construct($m, 418);
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
		parent::__construct($m, 501);
	}
}

class Error503 extends ErrorHTTP {
	public function __construct($m = NULL) {
		if (!$m) $m = 'Site Overlo[ar]d';
		parent::__construct($m, 503);
	}
}
