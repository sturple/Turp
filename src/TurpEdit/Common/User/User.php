<?php
namespace TurpEdit\Common\User;


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