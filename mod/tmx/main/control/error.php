<?php
if (($e = r3v\Vars::uri('error'))) {
	ob_clean();
	if (!is_numeric($e) || !class_exists('r3v\\Error'.$e))
		$e = 404;
	$e = 'r3v\\Error'.$e;
	$__error = new $e();
}

if (!($is_http = ($__error instanceof r3v\ErrorHTTP)))
	new r3v\Error500();


r3v\View::addToTitle('Oopsie no'.($is_http ? $__error->httpcode : 500));

return ['error' => $__error, 'is_http' => $is_http];
