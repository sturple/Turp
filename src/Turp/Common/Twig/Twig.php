<?php 

namespace Turp\Common\Twig;

use Symfony\Component\EventDispatcher\GenericEvent;

class Twig 
{
    public $twig;
    
    
    public $twig_vars = [];
    
    
    public $twig_paths;    
    
    
    protected $loader;
    public function __construct(\Turp\Common\Turp $turp)
    {
        $this->turp = $turp;
        $this->twig_paths = [];
    }    

    public function init() {
        $event = new GenericEvent(
            'twig',
            array('twig'=>$this)
        );
        $this->twig_paths[] = ROOT_DIR . $this->turp['settings']->value('twig.path');
       
        $this->loader = new \Twig_Loader_Filesystem($this->twig_paths);
        $params = $this->turp['settings']->value('twig.env');
        $this->twig = new \Twig_Environment($this->loader, $params);
        
        $this->twig->registerUndefinedFilterCallback(function ($name) {
            if (function_exists($name)) {
                return new \Twig_Filter_Function($name);
            }
            return new \Twig_Filter_Function(function () {
            });
        });        
        
        
        if ($this->turp['settings']->value('twig.env.debug')){
            $this->twig->addExtension(new \Twig_Extension_Debug());
        }

        $this->twig->addExtension(new \Turp\Common\Twig\TwigExtension());
        $this->turp['dispatcher']->dispatch('twig.EnvironmentLoaded' ,$event);
    }

    public function twig() {
        return $this->twig();
    }
    
    public function loader() {
        return $this->loader;
    }
    
    
}