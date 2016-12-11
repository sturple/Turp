<?php
namespace TurpEdit\Common;


class User 
{
    
    var $data = array();

    public function __construct($data=array())
    {    
        $this->data = $data;
    }
    
    public function save(){
        return $this->data;
    }
}