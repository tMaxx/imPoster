<?php
if (($e = rev\Vars::uri('error'))) {
	ob_clean();
	if (!is_numeric($e) || !class_exists('rev\\Error'.$e))
		$e = 404;
	$e = 'rev\\Error'.$e;
	$__error = new $e();
}

if (!($is_http = ($__error instanceof rev\ErrorHTTP)))
	new rev\Error500();


rev\View::addToTitle('Oopsie no'.($is_http ? $__error->httpcode : 500));

return ['error' => $__error, 'is_http' => $is_http];
