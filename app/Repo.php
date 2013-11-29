<?php ///teo /app/Repo.php
///Repository class
class Repo {
    ///Related object name
    static $OBJ;

    /**
     * Create and set object
     * @param $a array
     * @return \Model
     */
    public static function row(array $a) {
        $r = new static::$OBJ;
        $r->set($a);
        return $r;
    }

    ///Get $OBJ's table
    public static function table() {
        $t = static::$OBJ;
        return $t::table();
    }

    public static function getIdName() {
        return self::${strtolower(static::$OBJ).'_id'};
    }

    /**
     * Find object by given id
     * @param $id
     * @retun NULL|\Model
     */
    public static function findById($id) {
        $r = DB::row('SELECT * FROM '.self::table().' WHERE '.self::getIdName().'=?', 'i', array($id));
        if($r)
            return self::row($r);
        else
            return null;
    }

    ///Find all objects
    public static function findAll() {
        $rows = DB::rows('SELECT * FROM '.self::table());
        $r = array();
        foreach($rows as $v)
            $r[] = self::row($v);

        return $r;
    }

    /**
     * Find all objects by given rules
     * @param $where rules
     * @param $and or or
     * @return objects
     */
    public static function find(array $where, $and = TRUE) {
		if ($and) {
	       $param = '(1=1)';
	       $glue = ' and '
		} else {
		    $param = '(1=2)';
		    $glue = ' or ';
		}
        foreach ($where as $k => $v) {
        	//support multiple arguments
        	if (is_array($v))
        		$v = ' in ("'.implode('","', $v).'")';
        	else
        		$v = '='.$v;
        	$param .= $glue.$k.$v;
        }
        	//WOW!
        	//oj tam oj tam
        $rows = DB::rows('SELECT * FROM '.self::table().' WHERE '.$param);

        $ret = array();
		foreach ($rows as $row)
	        $ret[] = self::row($row);

        return $ret;
    }
}
