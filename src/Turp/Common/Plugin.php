<?php

namespace Turp\Common;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class Plugin implements EventSubscriberInterface
{
    protected $turp;
    protected $active = true;
    protected $routes;
    
    public static function getSubscribedEvents()
    {
        return array();
    }  
    
    public function subscribedEvents() {
        return array();
    }
    
    public function __construct($name,  $config=null){
       // $this->turp = Turp::instance();
        $this->name = $name;
    }    
    
    
    public function getRoutes() {
        return $this->routes;
    }
    
    public function setRoutes($routes){
        $this->routes = $routes;
    }
    
    public function setRoutesByFile($file){
        $routes = new \Turp\Common\Configuration();
        $routes->loadYaml($file);
        $this->routes = $routes;
    }
    
}