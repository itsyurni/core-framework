<?php
namespace yurni\framework;
use yurni\framework\View\Template;
use yurni\framework\Http\Request;
class View extends Application
{

    public static function render($view, $args = [])
    {
    
        $temp = new Template([
            "temp_path" => "../app/views/",
            "cache_path" => "../app/views/cache/",
            "optimize" => false,
        ]);
        

        
        return $temp->render($view,$args);
    }


    
}
