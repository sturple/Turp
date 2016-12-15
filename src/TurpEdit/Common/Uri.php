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
    
    var $container;
    var $request = null, $session=null;
    var $routeparams=array();
    var $content_type = 'text/html';
    var $site_routes = [

        
    ];

    public function __construct()
    {    

        $routes = new \TurpEdit\Common\Configuration();
        $routes->loadYaml(CONFIG_DIR.ROUTE_FILE);
        $this->site_routes = $routes->getItems();
        
        $this->container = \TurpEdit\Common\TurpEdit::instance();
        $this->request = Request::createFromGlobals();
        $this->session = $this->container['session'];
        
    }
    
    /*
    * Logic to determine if valid Route
    */
    public function getRoute(){
         //define routes
         $routes = new RouteCollection();
         foreach ($this->site_routes as $key =>$route){
             $this->container['log']->debug('route: '.$key,$route);
             $routes->add(
                 $key,
                 new Route(
                     $route['path'],
                     $route['defaults']
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
               $this->sendRedirect('/telogin/');
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
       return $this->render('index.twig');
    }
    
    private function actionProject() {
        return $this->render('index.twig');
    }
    private function actionAjax(){
        $this->content_type = 'application/json';
        //json_encode($data);;
    }
    
    private function actionSettings(){
        
    }
    
    private function actionLogin() {
        $data = [
            'content' => 'Login Page'
        ];        
        if ($this->session->has('csrftoken')){
            if ($this->request->get('csrf') == $this->session->get('csrftoken')){
                $this->session->remove('csrftoken');
                $this->session->remove('csrftoken2');
                if ($this->container['user']->authenticateUser($this->request) === true){
                    return $this->sendRedirect('/',302);
                };
            }            
        }

        return $this->render('login.twig',$data);
    }
    
    private function actionLogout() {
        //clears all session data
        $this->container['session']->invalidate();
        return $this->sendRedirect('/telogin/',302);
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
     
    private function sendRedirect($url, $code=301){
        $response =  new \Symfony\Component\HttpFoundation\RedirectResponse($url, $code);
        $response->send();
    }
    public function render($template,$data=array()){
        $response = new Response(
            'Content',
            Response::HTTP_OK,
            array('content-type'=> $this->content_type)
        );
        $response->setCharset('utf8');
        $response->setContent($this->container['twig']->render($template,$this->getTwigData($data)));
        $response->send();            
        

    }
}