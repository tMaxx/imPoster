<?php //r3vCMS /sys/_helpers.php
///Various functions that need to be included

///DB factory
function DB($var) {
	if (is_object($var) && ($var instanceof CMS\DB\Saveable))
		return new CMS\DB\Instance($var);
	elseif (is_string($var)) {
		if (substr_count($var, ' ') == 0)
			return new CMS\DB\Table($var);
		return new CMS\DB\Base($var);
	} else
		throw new CMS\DB\Error('Unsupported $var type');
}
