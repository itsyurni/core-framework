<?php
namespace yurni\framework\Router;
use yurni\framework\Application;
use yurni\framework\Http\Response;
use yurni\framework\Http\Request;
use yurni\framework\Exception\NotFoundException;
use yurni\framework\Router\action;

class Router {

    protected array $routes;
    protected Request $request;
    protected Response $response;
    protected array $handle;

    protected $patterns = [
        ':all' => '(.*)',
        ':id' => '(\d+)',
        ':int' => '(\d+)',
        ':number' => '([+-]?([0-9]*[.])?[0-9]+)',
        ':float' => '([+-]?([0-9]*[.])?[0-9]+)',
        ':bool' => '(true|false|1|0)',
        ':string' => '([\w\-_]+)',
        ':slug' => '([\w\-_]+)',
        ':uuid' => '([0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12})',
        ':date' => '([0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1]))',
    ];
    
    public function __construct(Application $app) 
    {
        $this->app = $app;
        $this->response = $this->app->response;
        $this->request = $this->app->request;
        $this->handle("404",function(){
            echo "Router Not Found !";
        });
    }

    public function handle($type,$callback){
        return $this->handle[$type] = $callback;
    }
    
    public function setPattern($patterns){
        foreach($patterns as $key => $val){
            $this->patterns[$key] = $val;
        } 
        return $this;
    }

    public function getHandle($type){
        return $this->app->container()->call($this->handle[$type]);
    }

    public function routeToRegex($route)
    {
        $route = preg_replace("/\\//","\/",$route);
        foreach($this->patterns as $key => $val)
        {
            $route = preg_replace("/\{".$key."\}/", $val, $route);
        }
        $route = preg_replace("/\{(.*)\}/", '$1', $route);
        $route = "/^" .$route. "$/i";
        return $route;
    }

    public function getRoutes()
    {
        return $this->routes;
    }

    public function register($method,$uri,$action)
    {
        $routeUri = $this->routeToRegex($uri);
        $route = new Route($method,$routeUri,$action); 
        $this->routes[] = $route;
        return $route;
    }

    protected function findRoute($path,$method)
    {
        foreach($this->getRoutes() as $route)
        {
            
            
            if(preg_match($route->getUri(),$path,$matches) && in_array($method,$route->getMethod()))
            {
                if(preg_match($route->getUri(),$path,$matches))
                {
                    foreach(array_reverse(array_slice($matches,1)) as $key => $val)
                    { 
                        $route->setParam($key, $val);
                    }
                }
                return $route;
            }
        }
        return false;
    }


    public function resolveCallback($callback,$args){
        $output = $this->app->container()->injectArgs($args)->call($callback);
        if(is_array($output))
            echo $this->response->json($output)->body();
        else
            echo $this->response->html($output)->body();
        return $this;
     
    }

    public function resolve()
    {
        $request_url = $this->request->getPath();
        $request_method = $this->request->getMethod();
        /**
        *  Search the Route Actuel
        *  
        */
        $route = $this->findRoute($request_url,$request_method);

        
        if($route)
        {
            /**
             *  Define the route in request Classe
             *  
             */

            $this->request->setRoute($route);

            /**
             *  get The Callback of the root
             *  
             */
            $routeCallback = $route->getCallback();

            /**
             *  get The Args & pm of the root
             *  
             */

            $routeArgs = $route->getParam();


            $getRouteMid = count($route->middlewares) > 0 ? $route->middlewares : false;

            if($getRouteMid){
                    foreach($getRouteMid as $key){
                        if(!$this->app->getMiddleware($key)){
                            return false;
                        }
                    }
                    return $this->resolveCallback($routeCallback,$routeArgs);
                
            }else{

                return $this->resolveCallback($routeCallback,$routeArgs);
            }
        }else{
            return $this->getHandle("404");
        }
    }

}