<?php
namespace yurni\framework\View;

class View
{

    public static function render($view, $args = [])
    {
        extract($args, EXTR_SKIP);


        $temp = new Template([
            "temp_path" => "../app/views/",
            "cache_path" => "../app/views/cache/",
            "optimize" => false,
        ]);
        $temp->render($view,$args);
    }


    
}
