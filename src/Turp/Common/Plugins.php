<?php

namespace Turp\Common;
class Plugins 
{
    protected $plugins = array();
    public function __construct(){
        $turp = Turp::instance();
        $config = $turp['settings'];
        $plugins = $config->value('plugins');
        //requires some sort of error checking
        if (is_array($plugins) ){
            foreach ($plugins as $plugin){
                $name = $plugin['name'];
                $class = $plugin['class'];
                $routesFile = $plugin['routes'];
                $instance = new $class(...array($name));
                $this->plugins[] = $instance;
                
                $reflector = new \ReflectionClass($class);
                $file = dirname($reflector->getFileName()). $routesFile;
                $instance->setRoutesByFile($file);
                $turp['log']->debug('reflection ' .print_R($file,true));
                $turp['dispatcher']->addSubscriber( $instance);
            }
        }
    }  
    
    public function getPlugins(){
        return $this->plugins;
    }
    
}