<?php ///revCMS /sys/DB.php
/**
 * DB - Database support class
 */
class DB extends NoInst
{
	//db object
	private static $db = NULL;
	//last executed query result
	private static $last = NULL;

	/**
	 * End execution, close everything
	 */
	public static function end()
	{
		self::$last->free();
		self::$db->close();
	}

	/**
	 * Prepare the DB and connect to it
	 * @param $con connection options
	 */
	public static function go(&$con)
	{
		if(self::$lockdown)
			return;

		if(!isset($con['host']) || !isset($con['user']) || !isset($con['pass']) || !isset($con['dbname']))
			throw new Exception('DB: Not sufficient connection parameters!');

		self::$db = new mysqli($con['host'], $con['user'], $con['pass'], $con['dbname']);
		unset($con);

		if(self::$db->connect_error)
			throw new Exception('DB: Error while connecting: '.self::$db->connect_errno);

		self::init();
	}

	/**
	 * Return no of rows affected by last query
	 * @return int
	 */
	public static function affectedRows()
	{
		return self::$db->affected_rows;
	}

	/**
	 * Return id of last insert
	 * @return int
	 */
	public static function insertedID()
	{
		return self::$db->insert_id;
	}

	/**
	 * Perform a query, w. binding if necessary
	 * @param $q query
	 * @param $tp types to bind
	 * @param $val values to bind
	 * @return bool|mysqli_result
	 */
	protected static function q($q, $tps = '', array $val = array())
	{
		if(!tps || !val)
			self::$last = $db->query($q)
		else {
			if(strlen($tps) != ($c = count($val)))
				throw new Exception('DB: Number of types != number of values!');

			if(!($st = self::$db->prepare($q)))
				throw new Exception('DB: Error while preparing query');

			if($c != $st->param_count)
				throw new Exception('DB: Number query params != number of values')

			for ($i = 0; $i < $c; $i++) {
				if($tps[$i] == 's')
					$val[$i] = self::$db->escape_string($val[$i]);
				$st->bind_param($tps[$i], $val[$i]);
			}

			//exec
			if(!($st->execute())
				throw new Exception('DB: Error while executing query');

			self::$last = $st->get_result();

			$st->close();
		}
		return self::$last;
	}

	/**
	 * Fetch a single row from db
	 * @param $q SQL query
	 * @param $types value types
	 * @param $values values to bind
	 */
	public static function row($q, $types = '', array $values = array())
	{
		$res = self::q($q, $types, $values);

		return $res->fetch_assoc();
	}

	/**
	 * Fetch a set of rows from db
	 * @param $q SQL query
	 * @param $types value types
	 * @param $values values to bind
	 */
	public static function rows($q, $types = '', array $values = array())
	{
		$res = self::q($q, $types, $values);
		
		$r = array()
		if (method_exists('mysqli_result', 'fetch_all'))
			$r = $res->fetch_all(MYSQLI_ASSOC);
		else
			while($tmp = $res->fetch_assoc())
				$r[] = $tmp;

		return $r;
	}


}
