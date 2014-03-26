<?php ///r3vCMS \r3v\HTTP
namespace r3v;

///HTTP support class
class HTTP {
	///HTTP headers to be sent through header('here');
	private static $headers = array();

	/**
	 * Append new headers to set
	 * @param $header
	 */
	public static function addHeaders($header) {
		if (!is_array($header))
			$header = [$header];

		foreach ($header as $k => $v) {
			if (!is_string($v))
				throw new Exception('Parameter is not a string!');
			if (is_numeric($k))
				self::$headers[] = $v;
			else
				self::$headers[$k] = $v;
		}
	}

	/**
	 * Set content-type header
	 * @param $type
	 * @return bool success
	 */
	public static function setContentType($type) {
		switch (strtolower($type)) {
			case 'js':
				$type = 'javascript';
			case 'pdf':
			case 'json':
			case 'javascript':
				$type = 'application/'.$type;
				break;
			case 'text':
				$type = 'text/plain';
				break;
			case 'plain':
			case 'html':
			case 'css':
				$type = 'text/'.$type;
				break;
			case 'jpeg':
			case 'gif':
			case 'png':
				$type = 'image/'.$type;
				break;
			default:
				return false;
				break;
		}
		self::$headers['content-type'] = 'Content-Type: '.$type.'; charset=utf-8';
		return true;
	}

	///Flush headers
	public static function flush() {
		foreach (self::$headers as &$v) {
			header($v);
			unset($v);
		}
	}

	public static function redirect($path) {
		throw new Redirect($path);
	}
}

Mod::registerUnload('HTTP', ['\\r3v\\HTTP::flush']);
