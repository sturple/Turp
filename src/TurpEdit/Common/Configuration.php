<?php
namespace TurpEdit\Common;

use TurpEdit\Common\Data\Data;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Yaml\Exception\ParseException;

class Configuration 
{
    var $config,
        $blacklist,
        $user;
    public function init() {
        try {
            // loading configurations
            $this->config = new Data(Yaml::parse(file_get_contents(CONFIG_DIR . CONFIG_FILE)));
            
            // loading blacklist
            $blacklistFile = $this->config->value('security.blacklist.file', false);
            if (false ==! $blacklistFile){
                $this->blacklist = new Data(Yaml::parse(file_get_contents(CONFIG_DIR . $blacklistFile)));
            }
           
        } catch (ParseException $e) {   
            print_R($e->getMessage());
        }
        
    }
}