<?php //r3vCMS /sys/_helpers.php
///Various functions that need to be included

///DB factory
function DB($var) {
	if (is_object($var) && ($var instanceof r3v\DB\Saveable))
		return new r3v\DB\Instance($var);
	elseif (is_string($var)) {
		if (substr_count($var, ' ') == 0)
			return new r3v\DB\Table($var);
		return new r3v\DB\Base($var);
	} else
		throw new r3v\DB\Error('Unsupported $var type');
}
