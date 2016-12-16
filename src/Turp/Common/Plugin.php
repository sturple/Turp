<?php

namespace Turp\Common\Plugin;


class Plugin 
{
    protected $te;
    
    protected $active = true;
    
    
    public function __construct($name, \Turp\Common\Turp $te, $config=null){
        $this->te = $te;
        $this->name = $name;
        if ($config){
            $this->config = $config;
        }
    }
    
    public static function getSubscribedEvents()
    {
        
    }
}