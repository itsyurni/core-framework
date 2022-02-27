<?php
namespace yurni\framework;
use yurni\framework\View;
abstract class Controller{

    protected static View $view;
    protected array $data = [];
    
    public function __construct($data = [])
    {
        $this->data = $data;
       
        self::$view = new View();
        
        
    }
    
   
} 
