<?php
namespace yurni\framework\Router;
use yurni\framework\Application;
use Reflector;
use ReflectionClass;
use ReflectionParameter;


class action
{
    protected Application $app;
    protected $action;
    protected Route $route;
    protected array $pm;
    public function __construct($app,$route){
        $this->app = $app;
        $this->route = $route;
        $this->action = $this->route->getCallback();
        $this->pm = $this->route->getParam();
    }

    public function build(){
        
        $getRouteMid = $this->route->middlewares[0] ?? false;
        if($getRouteMid){
            if($this->app->getMiddleware($getRouteMid)){
                return $this->app->getContainer()->setParam($this->pm)->get($this->action);
            }
        }else{
            
            return $this->app->getContainer()->setParam($this->pm)->get($this->action);
        }
        
     
        
    }
   
}