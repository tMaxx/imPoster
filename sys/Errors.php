<?php ///revCMS /sys/Error.php
/**
* Error
*/
class Error extends ErrorException {};

class ErrorHTTP extends Error {
	public function __construct($m = NULL, $hc = NULL) {
		$m = 'HTTP '.(int)$hc.': '.$m;

		parent::__construct($m);
	}
}
