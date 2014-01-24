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
		$count = mb_strlen($charset) - 1;
		while ($length--)
			$str .= $charset[mt_rand(0, $count)];
		return $str;
	}

	public static function truncate($string, $len = 140, $newline = FALSE) {
		if ($trim = (mb_strlen($string) > $len))
			$string = /*self::mb_*/wordwrap($string, $len);
		if (($newline || $trim) && ($newline = mb_strpos($string, "\n")))
			$string = mb_substr($string, 0, $newline);
		return $string;
	}

	/**
	 * @see http://stackoverflow.com/questions/3825226/
	 */
	public static function mb_wordwrap($str, $width = 75, $break = "\n", $cut = false) {
		$lines = explode($break, $str);
		foreach ($lines as &$line) {
			$line = rtrim($line);
			if (mb_strlen($line) <= $width)
				continue;
			$words = explode(' ', $line);
			$line = '';
			$actual = '';
			foreach ($words as $word) {
				if (mb_strlen($actual.$word) <= $width)
					$actual .= $word.' ';
				else {
					if ($actual != '')
						$line .= rtrim($actual).$break;
					$actual = $word;
					if ($cut) {
						while (mb_strlen($actual) > $width) {
							$line .= mb_substr($actual, 0, $width).$break;
							$actual = mb_substr($actual, $width);
						}
					}
					$actual .= ' ';
				}
			}
			$line .= trim($actual);
		}
		return implode($break, $lines);
	}

	/**
	 * Close HTML tags in truncated string
	 * @param $html input string
	 * @return string
	 */
	public static function basicCloseTags($html) {
		#put all opened tags into an array
		preg_match_all("#<([a-z]+)( .*)?(?!/)>#iU", $html, $result);
		$openedtags = $result[1];
		#put all closed tags into an array
		preg_match_all("#</([a-z]+)>#iU", $html, $result);
		$closedtags = $result[1];
		$len_opened = count($openedtags);
		# all tags are closed
		if (count($closedtags) == $len_opened)
			return $html;
		$openedtags = array_reverse($openedtags);
		# close tags
		for ($i = 0; $i < $len_opened; $i++) {
			if (!in_array($openedtags[$i], $closedtags))
				$html .= "</" . $openedtags[$i] . ">";
			else
				unset($closedtags[array_search($openedtags[$i], $closedtags)]);
		}
		return $html;
	}
}
