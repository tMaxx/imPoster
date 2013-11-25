<?php ///teo /app/Repo.php

class Repo {
    ///Related object name
    static $OBJ;

    public static function row(array $a)
    {
        new static::$OBJ;
        $OBJ->set($a);
        return $OBJ;
    }

    public static function findById($id)
    {
        
    }
}
