<?php

namespace TurpEdit\Common;

use Pimple\Container;
use TurpEdit\Common\Uri;

class TurpEdit extends Container
{
    protected static $instance;

    public static function instance(array $values = [])
    {
        if (!self::$instance) {
            self::$instance = static::load($values);
        } elseif ($values) {
            $instance = self::$instance;
            foreach ($values as $key => $value) {
                echo $key .' '.  $value . '</br>';
            }
        }
        return self::$instance;
    }
    
    
    protected static function load(array $values)
    {
        $container = new static($values);
        
        $container['editor']  = $container;
        // router
        $container['uri'] = function($c) {
          return new Uri();
        };
        // user
        $container['user'] = function($c) {
            return new User(); 
        };
        // session
        $container['session'] = function($c) {
            return array();
        };
        // projects
        $container['projects'] = function ($c) {
            return array();
        };
        $container['plugins'] = function ($c) {
            return array(); 
        };
        
        return $container;
    }
    
    public function process() {
        return ($this['uri']->getRoute());
        
        
    }

 
}