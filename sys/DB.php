<?php ///revCMS /sys/DB.php
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
		if(self::$lockdown)
			return;

		//! @todo fix
		$db = new mysqli();

		if($db->connect_errno)
			die('Error while connecting to DB');

		self::$lockdown = TRUE;
	}

	/**
	 * Prepare the DB and connect to it
	 * @param $params connection options
	 */
	static function go($params)
	{
		if(self::$lockdown)
			return;
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

	static function row()
	{
		$ac = func_num_args();
		$aa = func_get_args();
	}


}
