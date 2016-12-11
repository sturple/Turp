<?php

namespace TurpEdit\Common\Event;

use Symfony\Component\EventDispatcher\Event;
use TurpEdit\Common;

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
        $log = \TurpEdit\Common\TurpEdit::instance()['log'];
        $log->error('Resource Not Found ' . print_R($e,true));
    }

  
}