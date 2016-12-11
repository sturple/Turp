<?php

namespace TurpEdit\Common;

use Pimple\Container;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Session\Session;


use Monolog\Logger;
use Monolog\Handler\StreamHandler;

use TurpEdit\Common\Uri;
use TurpEdit\Common\Configuration;
use TurpEdit\Common\Event\TurpSubscriber;

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
        
        // session
        $container['session'] = function($c) {
            $session = new Session();
            $session->start();
            return $session;
        };            
        
        //monolog/logger setup
        $container['log'] = new Logger('TurpEdit');
        $container['log']->pushHandler(new StreamHandler(LOG_DIR.'error.log', Logger::DEBUG));
        
        // event dispacher;
        $container['dispatcher'] = new EventDispatcher();
        $container['dispatcher']->addSubscriber(new TurpSubscriber());
        
        // router
        $container['config'] = function($c) {
            return new Configuration($c);
        };
        $container['twig'] = function($c) {
            $path = ROOT_DIR.$c['config']->config->value('twig.path');
            $loader = new \Twig_Loader_Filesystem($path);
            $params = array();
            return  new \Twig_Environment($loader, $params);
            
        };
        $container['uri'] = function($c) {
          return new Uri($c);
        };
        // user
        $container['user'] = function($c) {
            return new User(); 
        };

        // projects
        $container['projects'] = function ($c) {
            return array();
        };
        $container['plugins'] = function ($c) {
            return array(); 
        };
        $container['log']->warning('Test');
        
        return $container;
    }
    
    public function process() {
        $this['config']->init();
        $this['uri']->response();
        
        
    }

 
}