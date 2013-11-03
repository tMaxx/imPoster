<?php
/**
 * DB - Database support class
 */
class DB extends NoInst
{
	//db object
	private static $db = NULL;
	//current query
	private static $qy = NULL;

	///Initialize object, connect to MySQL DB
	static function init()
	{
		//! @todo fix
		$db = new mysqli();

		if($db->connect_errno)
			die('Error while connecting to DB');
	}

	/**
	 * Perform a query
	 * @param $q query
	 * 
	 */
	static function query($q)
	{
		$this->qy = $db->query($q);
	}


}
