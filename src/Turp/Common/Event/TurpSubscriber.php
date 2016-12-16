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
        );
    }

    public function onResourceNotFound(Event $e)
    {
        $log = \Turp\Common\Turp::instance()['log'];
        $log->error('Resource Not Found ' . print_R($e,true));
    }

  
}
