<?php ///r3vCMS /sys/
namespace CMS;

/**
 * Sys - Static system helpers
 */
class Sys {
	/**
	 * Return string with cosequentive newlines/spaces limited to 2
	 * and with trimmed whitespaces at beginning and end of string
	 * @param string $input
	 * @param bool $tags strip html tags as well
	 * @return string
	 */
	public static function trim($input, $tags = FALSE) {
		if(!is_string($input))
			return $input;
		$input = trim($input);
		if($tags)
			$input = strip_tags($input);
		//trim cosequentive newlines (src: google/stackoverflow)
		$input = preg_replace('/(?:(?:\r\n|\r|\n)(?:\s|\t)*){2,}/s', "\n\n", $input);
		//trim spaces/tabs
		return preg_replace('/(?:\s|\t){2,}/s', '  ', $input);
	}

	/**
	 * Escape string
	 * @param $string
	 * @return escaped $string
	 */
	public static function eschtml($string, $html = TRUE) {
		// return $html ? htmlspecialchars($string) : ;
	}

	/**
	 * 
	 */
	public static function cast($types, $vals) {
		for ($i = 0, $c = count($vals); $i < $c; $i++) {
			switch ($types[i]) {
				case 'i':
					$val[$i] = (int) $val[$i];
					break;
				case 'd':
					$val[$i] = (double) $val[$i];
					break;
				case 'f':
					$val[$i] = (float) $val[$i];
					break;
				case 'c':
					$val[$i] = (string) $val[$i][0];
				case 's':
				default:
					$val[$i] = self::$db->escape_string($val[$i]);
					break;
			}
		}
	}

	public static function randString($length, $charset='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789') {
		$str = '';
		$count = strlen($charset) - 1;
		while ($length--)
			$str .= $charset[mt_rand(0, $count)];
		return $str;
	}
}
