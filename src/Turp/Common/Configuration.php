<?php
namespace Turp\Common;

use Turp\Common\Data\Data;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\EventDispatcher\Event;

class Configuration extends Data
{
    var $file;

    public function loadYaml($file){
        if (file_exists($file)){
            $contents = file_get_contents($file);
            if ($contents !== false ){
                $this->file = $file;
                try {
                    $this->setItems(Yaml::parse($contents));
                } catch (ParseException $e) {
                    // parce exception error
                }
            }
            else {
                // throw exception file does not exists
            }            
        }
    }
    
    public function saveYaml(){
        if (!empty($this->file)){
           $yaml = Yaml::dump($this->items);
           file_put_contents($this->file,$yaml);
           return true;
            
        }
        else {
            // throw exception
            print_R('Empty file');
        }
        return false;
    }
}