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
    var $routeparams=array();
    var $content_type = 'text/html';
    var $site_routes = [
        [
            'route'     => '_home',
            'path'      => '/',
            'auth'      => true,
            'callback'  => 'TurpEdit\Common\Uri::actionHome'
        ],
        [
            'route'     => '_ajax',
            'path'      => '/ajax/',
            'auth'      => true,
            'callback'  => 'TurpEdit\Common\Uri::actionAjax'
        ],
        [
            'route'     => '_settings',
            'path'      => '/settings/',
            'auth'      => true,
            'callback'  => 'TurpEdit\Common\Uri::actionSettings'
        ],
        [
            'route'     => '_login',
            'path'      => '/telogin/',
            'auth'      => false,
            'callback'  => 'TurpEdit\Common\Uri::actionLogin'
        ], 
        [
            'route'     => '_logout',
            'path'      => '/logout/',
            'auth'      => false,
            'callback'  => 'TurpEdit\Common\Uri::actionLogout'
        ]
    ];

    public function __construct()
    {    

        $this->container = \TurpEdit\Common\TurpEdit::instance();
        $this->request = Request::createFromGlobals();
        $this->session = $this->container['session'];
        $this->twig = $this->container['twig'];
        $this->getRoute();
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
                     array(
                        'controller'=>$route['callback'],
                        'auth'      =>$route['auth']
                     )
                     
            ));
        }
        // Getting Request and checking to see if they match
        
        $context = new RequestContext();
        $context->fromRequest($this->request);
        try {
            $matcher = new UrlMatcher($routes, $context);      
            $this->routeparams = $matcher->match($this->request->getPathInfo());
            
            // Check if Authenticated by session of User or if auth is not required.
            if ( ($this->session->has('user') and $this->session->get('user')->checkAuthentication()) or !$this->routeparams['auth'] ){
                //calls user function
                call_user_func($this->routeparams['controller']);
            }
            else {
                $this->actionLogin();
            }
            
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
     
    private function actionHome() {
       $this->content = $this->twig->render('index.twig',$this->getTwigData());
    }
    private function actionAjax(){
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
        if ($this->request->get('csrf') == $this->session->get('csrftoken')){
            $this->session->remove('csrftoken');
            $this->session->remove('csrftoken2');
            $redirect = $this->container['user']->authenticateUser($this->request);
        }
        $this->content = $this->twig->render('login.twig',$this->getTwigData($data));
    }
    
    private function actionLogout($p) {
        //clears all session data
        $this->container['session']->invalidate();
        $this->actionLogin();
    }
    
    private function getTwigData($data=array()){
        $data['settings']  = $this->container['settings'];
        $data['user'] = $this->container['session']->get('user');  
        $data = array_merge($data,$this->routeparams);
        return $data;
    }
    
    
    /***
     * 
     * Response
     *
     ***/
    public function res(){
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