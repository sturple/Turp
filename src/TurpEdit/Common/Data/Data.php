<?php

namespace TurpEdit\Common\Data;

class Data 
{
    protected $items;
    
    public function __construct(array $items = array())
    {
        $this->items = $items;
      
    }
    

    
    public function value($name, $default=null, $seperator='.')
    {
        return $this->get($name, $default, $seperator);
    }
    
    
    
    public function dump(){
        return $this->items;
    }
    public function get($name, $default = null, $separator ='.')
    {
        $path = explode($separator, $name);
        $current = $this->items;
        foreach ($path as $field) {
            if (is_object($current) && isset($current->{$field})) {
                $current = $current->{$field};
            } elseif (is_array($current) && isset($current[$field])) {
                $current = $current[$field];
            } else {
                return $default;
            }
        }
        return $current;
    }

    public function set($name, $value, $separator = null)
    {
        $path = explode($separator, $name);
        $current = &$this->items;
        foreach ($path as $field) {
            if (is_object($current)) {
                // Handle objects.
                if (!isset($current->{$field})) {
                    $current->{$field} = array();
                }
                $current = &$current->{$field};
            } else {
                // Handle arrays and scalars.
                if (!is_array($current)) {
                    $current = array($field => array());
                } elseif (!isset($current[$field])) {
                    $current[$field] = array();
                }
                $current = &$current[$field];
            }
        }
        $current = $value;
        return $this;
    }    

}