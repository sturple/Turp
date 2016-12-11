<?php
namespace TurpEdit\Common;

use TurpEdit\Common\Data\Data;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\EventDispatcher\Event;

class Configuration 
{
    var $config,
        $blacklist,
        $user,
        $container;
    
    
    public function __construct($container){
        $this->container = $container;
    }
    
    public function init() {
        $te = TurpEdit::instance();
        try {
            // loading configurations
            $this->config = new Data(Yaml::parse(file_get_contents(CONFIG_DIR . CONFIG_FILE)));
            
            // loading blacklist
            $blacklistFile = $this->config->value('security.blacklist.file', false);
            if (false ==! $blacklistFile){
                $this->blacklist = new Data(Yaml::parse(file_get_contents(CONFIG_DIR . $blacklistFile)));
            }
            $this->container['dispatcher']->dispatch('config.afterload');
           
        } catch (ParseException $e) {   
            $te['log']->error('Configuration Error ' . $e);
        }
        
    }
}