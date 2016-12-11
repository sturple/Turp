<?php
namespace TurpEdit\Common;

use Symfony\Component\EventDispatcher\Event;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class Uri 
{
    
    var $content ='';
    var $container;
    var $request = null, $session=null;
    var $twig;
    var $content_type = 'text/html';
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
        ],
        [
            'route'     => '_login',
            'path'      => '/telogin/',
            'callback'  => 'TurpEdit\Common\Uri::actionLogin'
        ]
    ];

    public function __construct($container)
    {    

        $this->container = $container;
        $this->request = Request::createFromGlobals();
        $this->session = $this->container['session'];
        $this->twig = $this->container['twig'];
        // Check if Authenticated.
        if (($this->session->has('user')) or true){
            $this->getRoute();
        }
        else {
            echo ' has no session';
            //$this->container['session']->set('user','admin');
        }

       
    }
    
    /*
    * Logic to determine if valid Route
    */
    private function getRoute(){
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
        // Getting Request and checking to see if they match
        
        $context = new RequestContext();
        $context->fromRequest($this->request);
        try {
            $matcher = new UrlMatcher($routes, $context);      
            $p = $matcher->match($this->request->getPathInfo());
            //calls user function
            call_user_func($p['controller']);
        }
        catch (\Symfony\Component\Routing\Exception\ResourceNotFoundException $e){
            $this->container['dispatcher']->dispatch('router.ResourceNotFounndException' );
            print_R($e->getMessage());
        }                    
    }
    
    /***
     * 
     * Route Actions 
     * 
     ***/
    private function actionAjax(){
        $data = array('message'=>'ajaxapplication');
        $data = $this->session->all();
        $this->content_type = 'application/json';
        $this->content = json_encode($data);;
    }
    
    private function actionSettings(){
        $this->content = 'actionsettings';
    }
    
    private function actionLogin() {
        $data = [
            'content' => 'Login Page'
        ];
        $this->content = $this->twig->render('login.twig',$data);
    }
    
    
    /***
     * 
     * Response
     *
     ***/
    public function response(){
        $response = new Response(
            'Content',
            Response::HTTP_OK,
            array('content-type'=> $this->content_type)
        );
        $response->setCharset('utf8');
        $response->setContent($this->content);
        $response->send();
    }
    
}