<?php
if (($e = r3v\Vars::uri('error'))) {
	ob_clean();
	if (!is_numeric($e) || !class_exists('r3v\\Error'.$e))
		$e = 404;
	$e = 'r3v\\Error'.$e;
	$__error = new $e();
}

r3v\View::addToTitle('Oopsie no'.$__error->httpcode);

return ['error' => $__error];
