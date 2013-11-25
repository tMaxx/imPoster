<?php //teo /app/Model.php

class Model {

	public function set(array $a)
	{
		foreach ($a as $k => $v)
			if(property_exists($this, $k))
				$this->$k = $v;
		return $this;
	}


	public function save()
	{
		$s = $this->toArray();
        
		if(isset($this->($prefix.'_id'));)
            DB::update($TABLE, $this->($prefix.'_id'), $s);  
        else
            DB::insert($TABLE, $s);   
	}

} 

