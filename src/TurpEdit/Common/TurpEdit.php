<?php

namespace TurpEdit\Common;

use Pimple\Container;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Session\Session;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use TurpEdit\Common\Uri;
use TurpEdit\Common\User\User;
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
        $container['settings'] = function($c) {
            $settings = new Configuration();
            $settings->loadYaml(CONFIG_DIR.CONFIG_FILE);
            return $settings;
        };
        $container['twig'] = function($c) {
            $path = ROOT_DIR.$c['settings']->value('twig.path');
            $loader = new \Twig_Loader_Filesystem($path);
            $params = $c['settings']->value('twig.env');
            $twig = new \Twig_Environment($loader, $params);
            $twig->addExtension(new \Twig_Extension_Debug());
            $twig->addFunction(
                new \Twig_SimpleFunction(
                    'form_csrf',
                    function($lock_to = null) use(&$c) {
                        
                        if (!$c['session']->has('csrftoken')) {
                            $c['session']->set('csrftoken', bin2hex(random_bytes(32)));
                        }
                        if (!$c['session']->has('csrftoken2')) {
                            $c['session']->set('csrftoken2', random_bytes(32));
                        } 
                        if (empty($lock_to)) {
                            return $c['session']->get('csrftoken');
                        }
                        return hash_hmac('sha256', $lock_to, $c['session']->get('csrftoken2'));
                    }
                )                
            );  
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
        $container['plugins'] = function ($c) {
            return array(); 
        };
        $container['uri'] = function($c) {
          return new Uri();
        };        
        return $container;
    }
    
    public function process() {
        $this['uri']->res();
    }
}