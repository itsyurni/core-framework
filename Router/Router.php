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
    
    public function __construct(Application $app,Request $req,Response $res) 
    {
        $this->app = $app;
        $this->response = $res;
        $this->request = $req;
        $this->response->setStatusCode(404);

        $this->handle("404",function(){
            echo "Router Not Found !";
        });
        

    }
    public function handle($type,$callback){
        return $this->handle[$type] = $callback;
    }
    

    public function getHandle($type){
        return $this->app->getContainer()->get($this->handle[$type]);
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



    public function register($method,$route,$action)
    {
    
        $route = new Route($method,$this->routeToRegex($route),$action); 
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


   
    public function resolve()
    {
        $request_url = $this->request->getPath();
        $request_method = $this->request->getMethod();
        $route = $this->findRoute($request_url,$request_method);
       
        if($route)
        {

            $getRouteMid = count($route->middlewares) > 0 ? $route->middlewares : false;
            
            if($getRouteMid){
                    foreach($getRouteMid as $key){
                        if(!$this->app->getMiddleware($key)){
                            return false;
                        }
                    }
                    return $this->app->getContainer()->setParam($route->getParam())->get($route->getCallback());
                
            }else{
                return $this->app->getContainer()->setParam($route->getParam())->get($route->getCallback());
            }
        }else{

            $this->response->setStatusCode(404);
            return $this->getHandle("404");
        }
    }

}