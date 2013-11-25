<?php ///teo /app/Repo.php

class Repo {
    ///Related object name
    static $OBJ;

    public static function row(array $a)
    {
        $r = new static::$OBJ;
        $r->set($a);
        return $r;
    }

    public static function findById($id)
    {
        
    }
}
