<?php
namespace TurpEdit\Common;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;


class Uri 
{
    
    var $content ='';
    var $site_routes = [
        [
            'route'     => '_ajax',
            'path'      => '/ajax/',
            'callback'  => 'TurpEdit\Common\Uri::actionAjax'
        ],
        [
            'route'     => '_settings',
            'path'      => '/settings/',
            'callback'  => 'TurpEdit\Common\Uri::actionSettings'
        ]
    ];

    public function __construct()
    {    
        //define routes
        $routes = new RouteCollection();
        foreach ($this->site_routes as $route){
            $routes->add(
                $route['route'],
                new Route(
                    $route['path'],
                    array('controller'=>$route['callback'])
            ));
        }

        $request = Request::createFromGlobals();
        $context = new RequestContext();
        $context->fromRequest($request);
        try {
            $matcher = new UrlMatcher($routes, $context);      
            $p = $matcher->match($request->getPathInfo());
            call_user_func($p['controller']);
        }
        catch (\Symfony\Component\Routing\Exception\ResourceNotFoundException $e){
            print_R($e->getMessage());
        }
       
    }
    
    
    public function actionAjax(){
        $this->content = 'actionajax';
    }
    
    public function actionSettings(){
        $this->content = 'actionsettings';
    }
    
    public function getRoute(){

        return $this->content;
    }
}