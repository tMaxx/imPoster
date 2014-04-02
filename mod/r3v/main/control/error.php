<?php
if (($e = r3v\Vars::uri('error'))) {
	ob_clean();
	if (!is_numeric($e) || !class_exists('Error'.$e))
		$e = 404;
	$e = 'Error'.$e;
	$__error = new $e();
}

r3v\View::addToTitle('Oopsie no'.$__error->httpcode);

return ['error' => $__error];
