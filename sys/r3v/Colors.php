<?php ///r3vCMS \r3v\Colors
namespace r3v;

/**
 * Colors for console
 * Maybe something else as well. But later.
 */
class Colors {
	const black				= "\033[0;30m";
	const white				= "\033[1;37m";
	const dark_grey		= "\033[1;30m";
	const light_grey		= "\033[0;37m";
	const grey				= self::light_grey;
	const dark_red			= "\033[0;31m";
	const light_red		= "\033[1;31m";
	const red				= self::light_red;
	const dark_green		= "\033[0;32m";
	const light_green		= "\033[1;32m";
	const green				= self::light_green;
	const dark_yellow		= "\033[0;33m";
	const light_yellow	= "\033[1;33m";
	const yellow			= self::light_yellow;
	const dark_blue		= "\033[0;34m";
	const light_blue		= "\033[1;34m";
	const blue				= self::light_blue;
	const dark_purple		= "\033[0;35m";
	const light_purple	= "\033[1;35m";
	const purple			= self::light_purple;
	const dark_cyan		= "\033[0;36m";
	const light_cyan		= "\033[1;36m";
	const cyan				= self::light_cyan;
	const reset				= "\033[0m";
	const none				= self::reset;
}
