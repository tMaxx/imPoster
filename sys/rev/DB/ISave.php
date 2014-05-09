<?php ///rev engine \rev\DB\ISave
namespace rev\DB;

/** Make a class saveable via DB\Instance */
interface ISave {
	public function toArray();
	public static function getKeyName();
	public function getId();
	public function getTableName();
}
