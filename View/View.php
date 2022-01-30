<?php
namespace yurni\framework\View;

class View
{

    public static function render($view, $args = [])
    {
        extract($args, EXTR_SKIP);


        $temp = new Template([
            "temp_path" => "../views/",
            "cache_path" => "../views/cache/",
            "optimize" => false,
        ]);
        $temp->render($view,$args);
    }


    
}
