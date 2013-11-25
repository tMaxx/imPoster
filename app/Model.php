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
        
		if(isset($this->getId()))
            DB::update($TABLE, $this->getId(), $s);  
        else
            DB::insert($TABLE, $s);   
	}
    
    public function getId()
    {
        if(isset($this->($prefix.'_id')))
            return $this->($prefix.'_id');
        else
            return NULL;
    }

} 

