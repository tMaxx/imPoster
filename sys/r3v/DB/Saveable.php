<?php ///r3vCMS /sys/DB/.php
namespace r3v\DB;

///Make a class saveable via DB\Instance
interface Saveable {
	public function toArray();
	public static function getKeyName();
	public function getId();
	public function getTableName();
}
