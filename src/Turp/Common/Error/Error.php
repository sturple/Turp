<?php 
namespace Turp\Common\Error;
use Symfony\Component\EventDispatcher\Event;

class Error extends Event
{
    public static function RouterError($error){
        $te = Turp::instance();
        $te['log']->error('Router error ' .$error);
        echo 'event';
    } 
    
}
