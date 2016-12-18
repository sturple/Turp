<?php

namespace Turp\Plugin;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\GenericEvent;


class Aceeditor extends \Turp\Common\Plugin
{
    
    public static function getSubscribedEvents()
    {
        return array(
            'router.MethodNotAllowedException' =>'onMethodNotAllowedException',
            'twig.EnvironmentLoaded' =>'onTwigEnvironmentLoaded',
        );
    }
    
  
    public function onMethodNotAllowedException(Event $e)
    {
        \Turp\Common\Turp::instance()['log']->debug('Method not allowed from plugin deveditor ' . print_R($e,true));
    }
    
    public function onTwigEnvironmentLoaded(GenericEvent $e){
       $turp = \Turp\Common\Turp::instance();
       $twig = $e->getArgument('twig');
       $twig_path = __DIR__ .'/resources/views/';
       try {
           $twig->loader()->prependPath($twig_path);
       }
       catch (\Twig_Error_Loader $e){
            $turp['log']->debug('Turp\Plugin\Deveditor:: '.$e->getMessage());
       }
       
      
    }
}