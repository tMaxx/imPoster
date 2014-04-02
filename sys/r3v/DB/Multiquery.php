<?php ///r3v engine \r3v\DB\Multiquery
namespace r3v\DB;

/**
 * Multi query executer
 * For long queries
 */
class Multiquery extends Base {

	protected $queries = [];
	protected $source = '';
	protected $type = '';

	protected function walkThroughSQL($file, $delimiter = ';') {
		if (File::fileExists($file) === true) {
			$file = fopen(ROOT.$file, 'r');

			if (is_resource($file) === true) {
				$query = array();

				while (feof($file) === false) {
					$query[] = fgets($file);

					if (preg_match('~' . preg_quote($delimiter, '~') . '\s*$~iS', end($query)) === 1) {
						$query = trim(implode('', $query));

						yield $query;
					}

					if (is_string($query) === true)
						$query = array();
				}

				return fclose($file);
			}
		}

		return false;
	}


	function __construct($src, $tp) {
		$this->source = $src;
		$this->type = $tp;
	}
}