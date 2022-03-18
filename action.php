<?php
namespace yurni\framework;
use yurni\framework\Router\Router;
use yurni\framework\Http\Request;
use yurni\framework\Http\Response;

class action {

    public $app;
    public $action;
    public function __construct($app,$action){
        $this->app = $app;
        $this->action = $action;
    }   

    public function getApp(){
        return $this->app;
    }
    
    public function getAction(){
        return $this->action;
    }
}