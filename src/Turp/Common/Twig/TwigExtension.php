<?php

namespace Turp\Common\Twig;

use Turp\Common\Turp;

class TwigExtension extends \Twig_Extension
{
    protected $turp;
    protected $debugger;
    
    public function __construct()
    {
        $this->turp == Turp::instance();
        
    }
    
    
    public function getName()
    {
        return 'TurpTwigExtension';
    }
    
    public function getGlobals()
    {
        return [
            'turp' => $this->turp,
        ];
    }
    public function getFilters() 
    {
        return [
            
        ];
    }
    
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('formcsrf', [$this, 'formcsrf']),
            /*
            new \Twig_SimpleFunction('debug', [$this, 'dump'], ['needs_context' => true, 'needs_environment' => true]),
            new \Twig_SimpleFunction('dump', [$this, 'dump'], ['needs_context' => true, 'needs_environment' => true]),*/
        ];
    }
    
    
    /***
     * 
     * 
     * Twig Simple Filters
     * 
     ***/
    
    public function formcsrf($lock_to = null)  {
        $turp = Turp::instance();
        if (!$turp['session']->has('csrftoken')) {
            $turp['session']->set('csrftoken', bin2hex(random_bytes(32)));
        }
        if (!$turp['session']->has('csrftoken2')) {
            $turp['session']->set('csrftoken2', random_bytes(32));
        } 
        if (empty($lock_to)) {
            return $turp['session']->get('csrftoken');
        }
        return hash_hmac('sha256', $lock_to, $turp['session']->get('csrftoken2'));
    }
    
    public function dump(\Twig_Environment $env, $context)
    {
        $debugger = Turp::instance()['debugger'];
        
        if (!$env->isDebug() || !$debugger) {
            return;
        }
       
        $count = func_num_args();
        if (2 === $count) {
            $data = [];
            foreach ($context as $key => $value) {
                if (is_object($value)) {
                    if (method_exists($value, 'toArray')) {
                        $data[$key] = $value->toArray();
                    } else {
                        $data[$key] = "Object (" . get_class($value) . ")";
                    }
                } else {
                    $data[$key] = $value;
                }
            }
            $debugger->addMessage($data, 'debug');
        } else {
            for ($i = 2; $i < $count; $i++) {
                $debugger->addMessage(func_get_arg($i), 'debug');
            }
        }
    }    
    
}

