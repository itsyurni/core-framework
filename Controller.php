<?php
namespace yurni\framework;
use yurni\framework\Http\Request;
use yurni\framework\db;

class Controller {
    
    public db $db;

    public function __construct(Application $app)
    {
       $this->app = $app;
       $this->db = new db;
       $this->request = $this->app->request;
       $this->response = $this->app->response;

    }
    public function render($view, $args = [])
    {
        $this->app->setViewAttr([
            "route" => $this->request->route()
        ]);
        
        $this->app->setViewAttr($args);
  
        return View::render($view,$this->app->getViewAttr());
    }


}