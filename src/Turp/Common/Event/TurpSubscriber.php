<?php

namespace Turp\Common\Event;

use Symfony\Component\EventDispatcher\Event;
use Turp\Common;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class TurpSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array(
            'router.ResourceNotFounndException' => 'onResourceNotFound',
            'router.MethodNotAllowedException' =>'onMethodNotAllowedException',
        );
    }

    public function onMethodNotAllowedException(Event $e)
    {
        $this->getLogger()->error('Method not allowed ' . print_R($e,true));
    }
    
    public function onResourceNotFound(Event $e)
    {
       $this->getLogger()->error('Resource Not Found ' . print_R($e,true));
    }
    
    public function getLogger(){
        return \Turp\Common\Turp::instance()['log'];;
    }
  
}
