<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Core\DB;

/**
 * Description of Result
 *
 * @author JackRabbit
 */
class Row
{
    public function __get($key)
    {
        if(!isset($this->$key)) return NULL;
        else return $this->$key;
    }
    
    public function __set($key, $value)
    {
        $this->$key = $value;
    }
    
    public function set_array($array)
    {
        foreach($array AS $key=>$value)
        {
            $this->__set($key, $value);
        }
    }
}
