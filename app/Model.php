<?php

class Model{
    private static $zmienna;
    
    public function set(array $a) {
        foreach($a as $key => $value){
           if(property_exists('Model', $key))
           {
               self::$zmienna = $value;
           }
                   
        }
        
}
    public function save() {
    
}

} 

