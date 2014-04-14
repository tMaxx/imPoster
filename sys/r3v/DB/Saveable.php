<?php ///rev engine \r3v\DB\Saveable
namespace r3v\DB;

///Make a class saveable via DB\Instance
interface Saveable {
	public function toArray();
	public static function getKeyName();
	public function getId();
	public function getTableName();
}
