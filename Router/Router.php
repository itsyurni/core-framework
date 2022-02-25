<?php
namespace yurni\framework\Router;
use yurni\framework\Application;
use yurni\framework\Http\Response;
use yurni\framework\Http\Request;
use yurni\framework\Exception\NotFoundException;


class Router {

    private array $routes = [];
    private Request $request;
    private Response $response;
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
        $this->handle["404"] = function(){
            echo "Router Not Found !";
        };
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
       
        $this->routes[] = new Route($method,$this->routeToRegex($route),$action); 
    }

    protected function findRoute($path,$method)
    {
        foreach($this->getRoutes() as $route)
        {
            
            
            if(preg_match($route->getUri(),$path,$matches) && in_array($method,$route->getMethod()))
            {
                if(preg_match($route->getUri(),$path,$matches))
                {
                    foreach(array_slice($matches,1) as $key => $val)
                    { 
                        $route->params[$key] = $val;
                    }
                }
                return $route;
            }
        }
        return false;
    }

    public function handle($type,$callback){
        return $this->handle[$type] = $callback;
    }
    

    public function getHandle($type){
        return $this->handle[$type]();
    }

    public function generateParameters(\Reflector $reflection, array $uriParams)
    {
        $parameters = [];
        foreach ($reflection->getParameters() as $key => $param) {
            $class = $param->getType() && !$param->getType()->isBuiltin() ? new \ReflectionClass($param->getType()->getName())
                : null;
            if (!is_null($class) && $class->isInstance($this->request)) {
                $parameters[] = $this->request;
            } elseif (!is_null($class) && $class->isInstance($this->response)) {
                $parameters[] = $this->response;
            }elseif (!is_null($class)) {
                $cl = $class->getName();
                if(class_exists($cl)){
                    $parameters[] = new $cl;
                }
            } else {
                
                if (empty($uriParams)) {
                    continue;
                }
                $uriParams = array_reverse($uriParams);
                $parameters[] = array_pop($uriParams);
                $uriParams = array_reverse($uriParams);
            }
        }

        return $parameters;
    }
   
    public function resolve()
    {
        $request_url = $this->request->getPath();
        $request_method = $this->request->getMethod();
        
        $route = $this->findRoute($request_url,$request_method);
       
        if($route)
        {
            $getAction = $route->getAction();
            $getPram = $route->getParam();
           
            if(is_array($route->getAction()))
            {


                if(class_exists($getAction[0]))
                {
                   
                    $getAction[0] = new $getAction[0]();
                    if(method_exists($getAction[0],$getAction[1]))
                    {
                        $reflection = new \ReflectionMethod($getAction[0],$getAction[1]);
                        $parameters = $this->generateParameters($reflection, $getPram);
                        return call_user_func_array($getAction,$parameters);
                    }else{
                        throw new NotFoundException("Method <b>\"{$getAction[1]}\"</b> not found !");
                    }
                }else{
                    throw new NotFoundException("Controller <b>\"{$getAction[0]}\"</b> not found !");
                }
			}

			if(is_callable($getAction))
            {
                $reflection = new \ReflectionFunction($getAction);
                $parameters = $this->generateParameters($reflection, $getPram);
				echo call_user_func_array($getAction, $parameters);
			}
            
        }else{
            $this->response->setStatusCode(404);
            return $this->getHandle("404");
        }
    }

}