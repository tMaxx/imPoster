<?php ///revCMS /sys/Error.php
/**
 * Error
 */
class Error extends ErrorException {};

class ErrorHTTP extends Error {
	public function __construct($msg = NULL, $code = NULL, $add = NULL) {
		$msg = 'HTTP '.(int)$code.': '.$msg;

		parent::__construct($msg);
	}
}

class Error404 extends ErrorHTTP {
	public function __construct($m = NULL, $add = NULL) {
		parent::__construct($m, 404, $add);
	}
}

class Error403 extends ErrorHTTP {
	public function __construct($m = NULL, $add = NULL) {
		parent::__construct($m, 403, $add);
	}
}

class Error400 extends ErrorHTTP {
	public function __construct($m = NULL) {
		parent::__construct($m, 400);
	}
}

class Error500 extends ErrorHTTP {
	public function __construct($m = NULL) {
		parent::__construct($m, 500);
	}
}

class Error503 extends ErrorHTTP {
	public function __construct($m = NULL) {
		parent::__construct($m, 503);
	}
}