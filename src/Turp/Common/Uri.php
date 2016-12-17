<?php
namespace Turp\Common;

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
    
    var $turp;
    var $request = null;
    var $routeparams=array();
    var $content_type = 'text/html';
    var $site_routes = [];
    var $routeCollection;

    public function __construct()
    {
        $routes = new \Turp\Common\Configuration();
        $routes->loadYaml(CONFIG_DIR.ROUTE_FILE);
        $this->site_routes = $routes->getItems();
        
        $this->turp = \Turp\Common\Turp::instance();
        $this->request = Request::createFromGlobals();
    }

    /*
    * Logic to determine if valid Route
    */
    public function getRoute(){
         //define routes
        $this->configureRouter();
        // Getting Request and checking to see if they match

        $context = new RequestContext();
        $context->fromRequest($this->request);
        try {
            $matcher = new UrlMatcher($this->routeCollection, $context);      
            $this->routeparams = $matcher->match($this->request->getPathInfo());
            
            // Check if Authenticated by session of User or if auth is not required.
            if ( ($this->turp['session']->has('user') and $this->turp['session']->get('user')->checkAuthentication()) or !$this->routeparams['auth'] ){
                //calls user function
                call_user_func($this->routeparams['controller']);
            }
            else {
               $this->sendRedirect('/login/');
            }
        }
        catch (\Symfony\Component\Routing\Exception\ResourceNotFoundException $e){
            $this->turp['dispatcher']->dispatch('router.ResourceNotFounndException' );
        }      
        catch (\Symfony\Component\Routing\Exception\MethodNotAllowedException $e){
            $this->turp['dispatcher']->dispatch('router.MethodNotAllowedException' );
        }
    }
    
    private function configureRouter(){
        $this->routeCollection = new RouteCollection();
         foreach ($this->site_routes as $key =>$route){
             $requirements = empty($route['requirements']) ? [] : $route['requirements'];
             $options = empty($route['options']) ? [] : $route['options'];
             $host = empty($route['host']) ? '' : $route['host'];
             $schemes = empty($route['schemes']) ? [] : $route['schemes'];
             $methods = empty($route['methods']) ? [] : $route['methods'];
             
             $this->routeCollection->add(
                 $key,
                 new Route(
                     $route['path'],
                     $route['defaults'], //defaults
                     $requirements,
                     $options,
                     $host,
                     $schemes,
                     $methods
                     
                     
            ));
        }        
    }
    
    /**
     *  Diagnostic function console command
     **/
    public function getRouteCollection() {
        $this->configureRouter();
        return $this->routeCollection;
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
        if ($this->turp['session']->has('csrftoken')){
            if ($this->request->get('csrf') == $this->turp['session']->get('csrftoken')){
                $this->turp['session']->remove('csrftoken');
                $this->turp['session']->remove('csrftoken2');
                if ($this->turp['user']->authenticateUser($this->request) === true){
                    return $this->sendRedirect('/',302);
                }
            }            
        }

        return $this->render('login.twig',$data);
    }
    
    private function actionLogout() {
        //clears all session data
        $this->turp['session']->invalidate();
        return $this->sendRedirect('/login/',302);
    }
    
    private function getTwigData($data=array()){
        $data['settings']  = $this->turp['settings'];
        $data['user'] = $this->turp['session']->get('user');  
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
        $response->setContent($this->turp['twig']->twig->render($template,$this->getTwigData($data)));
        $response->send();            
        

    }
}