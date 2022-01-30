<?php
namespace yurni\framework;

use yurni\framework\Http\Request;
use yurni\framework\Http\Response;
use yurni\framework\Router\Router;

class Application {

    public static string $ROOT_DIR;
    protected Router $route;
    const VERSION = '0.0.1';

    public function __construct(array $container = []) {
        $this->route = new Router($this);
        $this->container = $container;
    }
    public function getContainer()
    {
        return $this->container;
    }


    /********************************************************************************
     * Router proxy methods
     *******************************************************************************/

    /**
     * Add GET route
     *
     * @param  string $pattern  The route URI pattern
     * @param  callable|string  $callable The route callback routine
     *
     * @return \Yurni\Router\Route
     */

    public function get($path,$callback){
        return $this->route->register("get",$path,$callback);
    }
  
    /**
     * Add Post route
     *
     * @param  string $pattern  The route URI pattern
     * @param  callable|string  $callable The route callback routine
     *
     * @return \Yurni\Router\Route
     */

    public function post($path,$callback){
        return $this->route->register("post",$path,$callback);   
    }  

    public function run(){
        return $this->route->resolve();
    }
}