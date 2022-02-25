<?php
namespace yurni\framework\Router;

class Route {

    private $name;
    
    private $action;

    private $method;

    private $uri;

    public $params = [];

    public function  __construct($method,$uri,$action)
    {
        $this->action = $action;
        $this->method = $method;
        $this->uri = $uri;
    }

    public function getAction()
    {
        return $this->action;
    }
    
    public function getMethod()
    {
        return $this->method;
    }
    
    public function getUri()
    {
        return $this->uri;
    }
        
    public function getParam()
    {
        return $this->params;
    }
                

}
