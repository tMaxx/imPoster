<?php
/**
* H - Static helpers
*/
class H extends NoInst
{
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




}