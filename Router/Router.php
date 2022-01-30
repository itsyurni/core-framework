<?php
namespace yurni\framework\Router;
use yurni\framework\Application;
use yurni\framework\Http\Response;
use yurni\framework\Http\Request;

class Router {

    public array $routes = [];
    public Request $request;
    public Response $response;
    public array $param = [];

    public function __construct(Application $app) 
    {
        $this->response = new Response($app,$this);
        $this->request = new Request($app,$this);
    }

    public function routeToRegex($route)
    {
        $route = preg_replace("/\\//","\/",$route);
        $route = preg_replace('/\{([a-z]+):int\}/', '(?P<\1>\d+)', $route);
        $route = preg_replace('/\{([a-z]+):char\}/', '(?P<\1>.+)', $route);
        $route = preg_replace('/\{([a-z]+):str\}/', '(?P<\1>[A-z]+)', $route);
        $route = "/^" .$route. "$/i";
        return $route;
    }

    public function getRoutes()
    {
        return $this->routes;
    }
    
    public function register($method,$route,$callback)
    {
        $this->routes[$method][$this->routeToRegex($route)] = $callback; 
    }

    protected function matchRoot($path,$method)
    {
        foreach($this->getRoutes()[$method] as $route => $callback){
            if(preg_match($route,$path,$matches)){
                foreach($matches as $key => $val){ 
                    if(is_string($key))
                        $this->param[$key] = $val;
                }
                $this->callback = $callback;
                return true;
            }
        }
        return false;
    }

    public function paramList()
    {
        $list = [$this->request,$this->response];
        foreach($this->param as $data => $userData){
            array_push($list ,$userData);
        } 
        return $list;
    } 

    public function resolve()
    {
        $request_url = $this->request->getPath();

        $request_method = $this->request->getMethod();
       
        if($this->matchRoot($request_url,$request_method)){
            
            if(is_array($this->callback)){
                $this->callback[0] = new $this->callback[0]();
				return call_user_func_array($this->callback,$this->paramList());
			}
			if(is_callable($this->callback)){
				echo call_user_func_array($this->callback,$this->paramList());
			}

            
        }else{
            $this->response->setStatusCode(404);
            echo "Not Found !";
            exit();
        }
    }
}