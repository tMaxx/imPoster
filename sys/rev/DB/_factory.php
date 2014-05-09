<?php ///rev engine \rev\DB\DB
namespace rev\DB;

/**
 * Return predicted DB object based on input
 * @param $var
 * @return class rev\DB\{??}
 * @throws rev\DB\Error if input is unrecognized
 */
function Q($var) {
	if (is_object($var) && ($var instanceof rev\DB\Saveable))
		return new Instance($var);
	elseif (is_string($var)) {
		if (substr_count($var, ' ') == 0)
			return new Table($var);
		return new Base($var);
	} else
		throw new Error('Unsupported $var type');
}
