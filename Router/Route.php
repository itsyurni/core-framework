<?php
namespace yurni\framework\Router;
use yurni\framework\Http\Request;
use yurni\framework\Http\Response;

class Route {
    public $path;
    public function __construct($path,$callback,$args = []){
      $this->path = $path;
      $this->callback = $callback;
      $this->args = [];
      foreach($args as $arg){
          if($arg instanceof res){
             return array_push($this->args,new res);
          }
          array_push($this->args,$arg);
  
      }
  
    }
    public function __call($method, $args) {
       return call_user_func_array($this->callback,$this->args); 
      
  
    }
  }