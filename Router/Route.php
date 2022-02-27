<?php
namespace yurni\framework\Router;

class Route {

    
    protected $callback;

    protected $method;

    protected $uri;

    protected array $params;

    public $middlewares = array();
    
    public function  __construct($method,$uri,$callback)
    {
        $this->callback = $callback;
        $this->method = $method;
        $this->uri = $uri;
 
    }

    public function getCallback()
    {
        return $this->callback;
    }
    
    public function getMethod()
    {
        return $this->method;
    }

    public function isPost()
    {
        return $this->getMethod() == "post" ? true : false;
    }
 
    public function isPut()
    {
        return $this->getMethod() == "put" ? true : false;
    }

    public function isGet()
    {
        return $this->getMethod() == "get" ? true : false;
    }   

    public function isPatch()
    {
        return $this->getMethod() == "patch" ? true : false;
    }

    public function isAny()
    {
        return $this->getMethod() == "any" ? true : false;
    }
    public function isDelete()
    {
        return $this->getMethod() == "delete" ? true : false;
    }
    public function isOnly()
    {
        return $this->getMethod() == "only" ? true : false;
    }
    public function getUri()
    {
        return $this->uri;
    }
        
    public function getParam()
    {
        return $this->params ?? [];
    }
    public function setParam($key,$val){
        $this->params[$key] = $val;
        return $this;
    }
    public function middleware($middlewares)
    {
        $this->middlewares = array_merge($this->middlewares, (array) $middlewares);
        return $this;
    }
    public function __call($method, $params)
    {
        return $this->middleware($method);
    }   

}
