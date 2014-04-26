<?php ///rev engine \rev\DB\Saveable
namespace rev\DB;

///Make a class saveable via DB\Instance
interface Saveable {
	public function toArray();
	public static function getKeyName();
	public function getId();
	public function getTableName();
}
