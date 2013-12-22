<?php ///revCMS /sys/Error.php
///Error class
class Error extends ErrorException {
	/**
	 * Prettify trace
	 * @param $trace
	 * @return string
	 */
	public static function prettyTrace($trace) {
		$result = array('<br />Stack trace:<br />');
		foreach($trace as $i => $v) {
			$result[] = $i.'# ';

			if (isset($v['file']) && $v['file'])
				$result[] = pathdiff($v['file']).':'.$v['line'].' - ';
			else
				$result[] = '[<i>internal call</i>] ';

			if (isset($v['class']))
				$result[] = $v['class'].$v['type'];

			$result[] = $v['function'].'()';

			if (isset($v['args']) && $v['args'])
				$result[] = ' argv'.htmlspecialchars(pathdiff(json_encode($v['args'], JSON_UNESCAPED_SLASHES|JSON_NUMERIC_CHECK)), ENT_COMPAT|ENT_HTML5);

			$result[] = '<br />';
		}
		return implode('', $result);
	}

	/**
	 * Custom all-error handler
	 * @param [standard params]
	 * @return varies
	 */
	public static function h($eno = NULL, $estr = NULL, $efile = NULL, $eline = NULL, $econtext = NULL) {
		static $constants;
		if (!isset($constants)) {
			$constants = get_defined_constants(1);
			$constants = $constants['Core'];
		}

		$trace = debug_backtrace();
		$result = array('<br /><br />');

		if ((isset($eno, $estr, $efile)) || (!isset($eno) && (($e_last = error_get_last()) !== NULL))) { //error
			$eName = '?';

			if (isset($e_last['type']))
				$eno = $e_last['type'];

			foreach ($constants as $key => $value)
				if (substr($key, 0, 2) == 'E_' && $eno == $value) {
					$eName = $key;
					array_shift($trace);
					break;
				}

			if (isset($e_last['message'])) {
				$eName = '<b>FATAL</b>: '.$eName;
				$efile = $e_last['file'];
				$eline = $e_last['line'];
				$estr = $e_last['message'];
			}

			$result[] = '<big><b>Error</b></big>: '.$eName.': '.$estr.' at '.pathdiff($efile).':'.$eline;
		}
		elseif (isset($eno)) { //exception handler
			if ($eno instanceof Error)
				$result[] = '<big><b>Error</b></big>: ';
			elseif ($eno instanceof Exception)
				$result[] = '<big><b>Exception</b></big>: ';
			elseif ($eno instanceof ErrorException)
				$result[] = '<big><b>Error/Exception</b></big>: ';

			$result[] = $eno->getMessage().' at '.pathdiff($eno->getFile()).':'.$eno->getLine();

			$trace = $eno->getTrace();
		}
		else {
			if (!isset($e_last))
				CMS::end();
			return;
		}

		if ((isset($eno) && !($eno instanceof ErrorHTTP)) || isset($e_last)) {
			http_response_code(500);
			CMS::flushHeaders();
		}

		if ($trace)
			$result[] = Error::prettyTrace($trace);

		echo implode('', $result);
	}
}

class ErrorCMS extends Error {};
class ErrorDB extends Error {};

class ErrorHTTP extends Error {
	private $httpcode;
	private $inmessage;
	public function __construct($msg = NULL, $code = NULL, $add = NULL) {
		$this->httpcode = $code;
		$this->inmessage = $msg;

		if($code)
			http_response_code($code);

		$msg = 'HTTP '.(int)$code.': '.$msg;

		parent::__construct($msg);
	}

	public function getHttpCode() {
		return $this->httpcode;
	}

	public function getFancyMessage() {
		return '<h1 class="white">HTTP '.$this->httpcode.'</h1>'.$this->inmessage.'';
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

class Error503 extends ErrorHTTP {
	public function __construct($m = NULL) {
		if (!$m) $m = 'Site Overlo[ar]d';
		parent::__construct($m, 503);
	}
}

class Redirect extends ErrorHTTP {
	public function __construct($target = NULL) {
		if (!$target)
			throw new ErrorCMS('No redirect specified');
		CMS::addHeader('Location: '.$target);
		die(); //die nicely
	}
}
