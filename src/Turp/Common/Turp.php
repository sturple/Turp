<?php

namespace Turp\Common;

use Pimple\Container;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Finder\Finder;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Turp\Common\Uri;
use Turp\Common\User\User;
use Turp\Common\Twig\Twig;
use Turp\Common\Configuration;
use Turp\Common\Event\TurpSubscriber;
use Turp\Common\Plugin;

class Turp extends Container
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
        $whoops = new \Whoops\Run;
        $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
        $whoops->register();
 
        $container['turp']  = $container;

        // session
        $container['session'] = function($c) {
            $session = new Session();
            $session->start();
            return $session;
        };
        $container['debugger'] = function($c) {
            return null;

        };

        //monolog/logger setup
        $container['log'] = new Logger('Turp');
        $container['log']->pushHandler(new StreamHandler(LOG_DIR.'error.log', Logger::DEBUG));
        
        
        
        $container['plugin'] = function ($c) {
            return new Plugin('turp.core'); 
        };        

        // event dispacher;
        $container['dispatcher'] = function($c){
            $ed  = new EventDispatcher();
            $ed->addSubscriber(new TurpSubscriber());
            $ed->addSubscriber($c['plugin']);
            return $ed;
        };

        // router
        $container['settings'] = function($c) {
            $settings = new Configuration();
            $settings->loadYaml(CONFIG_DIR.CONFIG_FILE);
            return $settings;
        };
        $container['twig'] = function($c) {
            $twig = new Twig($c);
            $twig->init();
            return $twig;
        };

        // user
        $container['user'] = function($c) {
            return new User(array('active'=>'false')); 
        };

        // projects
        $container['projects'] = function ($c) {
            return array();
        };

        $container['uri'] = function($c) {
          return new Uri();
        };   
        
        $container['plugins'] = function($c){
            return new Plugins();
        };
        
        // autoload plugins
        spl_autoload_register(function($class) use($container){
            $prefix = "Turp\\Plugin";
            if (false !== strpos($class, $prefix)) {
                $class = substr($class, strlen($prefix));
                $path = strtolower(ltrim(preg_replace('#\\\|_(?!.+\\\)#', '/', $class), '/'));
            
                $file = PLUGIN_DIR. "{$path}/{$path}.php";
                if (file_exists($file)){
                    return include_once($file);
                }
            }
            return false;
            
        });       
        
        
        return $container;
    }
    public function process() {
        $this['plugins'];
        $this['uri']->getRoute();
    }
}