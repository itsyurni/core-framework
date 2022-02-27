<?php
namespace yurni\framework;
use yurni\framework\Router\Router;
use yurni\framework\Http\Request;
use yurni\framework\Http\Response;

class Application {

    protected Router $router; 
    protected Request $request;
    protected Response $response;
    protected array $config;
    protected container $container;
    protected $middlewares = array();
    public function __construct($conf = []) 
    {
        $dotenv = \Dotenv\Dotenv::createImmutable('../');
        $dotenv->load();
        
        $this->request = new Request();
        $this->response = new Response();
        $this->router = new Router($this,$this->request,$this->response);
        $this->config = $conf;
        $this->container = new container;

 
    }

    public function setMiddleware($name, $callable)
    {
        $this->middlewares[$name] = $callable;
    }     

    public function getMiddleware($name)
    {
        return $this->hasMiddleware($name) ? $this->getContainer()->get($this->middlewares[$name]) : false;
    }  
    public function hasMiddleware($name)
    {
        return isset($this->middlewares[$name]);
    }


    
    public function getContainer()
    {
        return $this->container;
    }       
    public function getRouter()
    {
        return $this->router;
    }
      
    public function getResponse()
    {
        return $this->response;
    }
            
    public function getRequest()
    {
        return $this->request;
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

    public function get($path,$callback)
    { 
        return $this->router->register(["get"],$path,$callback);
    }
  
    /**
     * Add Post route
     *
     * @param  string $pattern  The route URI pattern
     * @param  callable|string  $callable The route callback routine
     *
     * @return \Yurni\Router\Route
     */

    public function post($path,$callback)
    {
        return $this->router->register(["post"],$path,$callback);   
    }  
    
    /**
     * Add Patch route
     *
     * @param  string $pattern  The route URI pattern
     * @param  callable|string  $callable The route callback routine
     *
     * @return \Yurni\Router\Route
     */

    public function patch($path,$callback)
    {
        return $this->router->register(["patch"],$path,$callback);   
    } 
    /**
     * Add Put route
     *
     * @param  string $pattern  The route URI pattern
     * @param  callable|string  $callable The route callback routine
     *
     * @return \Yurni\Router\Route
     */

    public function put($path,$callback)
    {
        return $this->router->register(["put"],$path,$callback);   
    } 
      
    /**
     * Add delete route
     *
     * @param  string $pattern  The route URI pattern
     * @param  callable|string  $callable The route callback routine
     *
     * @return \Yurni\Router\Route
     */

    public function delete($path,$callback)
    {
        return $this->router->register(["delete"],$path,$callback);   
    }   
    /**
     * Add any route
     *
     * @param  string $pattern  The route URI pattern
     * @param  callable|string  $callable The route callback routine
     *
     * @return \Yurni\Router\Route
     */

    public function any($path,$callback)
    {
        return $this->router->register(["post","get","put","delete","patch"],$path,$callback);   
    }  
      
    /**
     * Add Only route
     *
     * @param  string $pattern  The route URI pattern
     * @param  callable|string  $callable The route callback routine
     *
     * @return \Yurni\Router\Route
     */

    public function only($methods = [],$path,$callback)
    {
        return $this->router->register($methods,$path,$callback);   
    }  
    public function run()
    {
        try{
            return $this->router->resolve();
        }catch(\Exception $e){
            echo "ERROR : " .$e->getMessage();
        }
        
    }
}