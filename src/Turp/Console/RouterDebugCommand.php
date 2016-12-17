<?php

namespace Turp\Console;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Routing\Route;

class RouterDebugCommand extends ConsoleCommand
{
    protected function configure()
    {
        $this
            ->setName('debug:router')
            ->setAliases(
                [
                    'router:debug'
                ]
            )
            ->setDefinition(
                [
                    new InputArgument('name', InputArgument::OPTIONAL, 'A route name'),
                ]
            )
            ->setDescription('Displays Current routes for an application')
            ->setHelp("The <info>%command.name%</info> displays the configured routes:<info>php %command.full_name%</info>")
        ;
    }
    
    protected function serve()
    {
        $name = $this->input->getArgument('name');
        $routes = \Turp\Common\Turp::instance()['uri']->getRouteCollection();
        $header = str_pad('',40,'-').' '. str_pad('',10,'-').' '.str_pad('',10,'-').' '.str_pad('',10,'-').' '.str_pad('',10,'-'). ' ' .str_pad('',20,'-');
        $this->output->writeln($header);
        $this->output->writeln('<green>'.str_pad('Name',40).' '. str_pad(' Method',10).' '.str_pad(' Scheme',10).' '.str_pad(' Host',10).' '.str_pad(' Auth',10). ' ' .str_pad(' Path',20).'</green>');
        $this->output->writeln($header);
        if (false){
            
        }
        else {
            foreach ($routes as $key=>$route){
                if ($route->hasDefault('controller')){
                    try {
                        
                        $name =  str_pad($key. ' ',40);
                        $method = str_pad(implode($route->getMethods(),' '),10);
                        $scheme = str_pad(implode($route->getSchemes(),' '),10);
                        $host = str_pad($route->getHost(),10);
                        $auth = str_pad( ' '.($route->getDefault('auth') ? 'TRUE' : 'FALSE'),10 );
                        $path = $route->getPath();
                       
                        $this->output->writeln("{$name} {$method} {$scheme} {$host} {$auth} {$path}");
                        
                    }
                    catch (\InvalidArgumentException $e) {
                        
                    }
                }
            }
        }
        $this->output->writeln($header);
        
    }
    
    private function fixlength($str, $maxlen=0){
        $len = strlen($str);
        
    }
}