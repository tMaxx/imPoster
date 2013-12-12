<?php //teo /app/Model.php

class Model {
	static $TABLE;

	public function set(array $a) {
		foreach ($a as $k => $v)
			if(property_exists($this, $k))
				$this->$k = $v;
		return $this;
	}

	public function save() {
		$s = $this->toArray();

		if($this->getId())
			DB::update($this->table(), $this->getId(), $s);
		else
			DB::insert($this->table(), $s);
	}

	public static function getPK() {
		return strtolower(get_called_class()).'_id';
	}
	
	public function getId() {
		if(isset($this->{static::getPK()}))
			return $this->{static::getPK()};
		else
			return NULL;
	}
	
	public function table() {
		return static::$TABLE;
	}

} 

