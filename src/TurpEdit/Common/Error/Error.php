<?php 
namespace TurpEdit\Common\Error;
use Symfony\Component\EventDispatcher\Event;

class Error extends Event
{
    public static function RouterError($error){
        $te = TurpEdit::instance();
        $te['log']->error('Router error ' .$error);
        echo 'event';
    } 
    
}