<?php
namespace yurni\framework;
use Reflector;
use ReflectionClass;
use ReflectionParameter;
use ReflectionMethod;
use ReflectionFunction;
use yurni\framework\Exception\NotFoundException;

class container {
    protected $callback;
    protected array $args = [];

    public function injectArgs($args)
    {
        foreach($args as $key => $val){
            $this->args[$key] = $val;
        }
        return $this;
    }
    public function generateArgs(Reflector $reflection)
    {

        
        $parameters = [];
        
        if(method_exists($reflection,"getParameters")){
            $reflect = $reflection;
        }else{
            $reflect = $reflection->getConstructor();
        }
       
        foreach ($reflect->getParameters() as $key => $param) {

            if($param->getType() && !$param->getType()->isBuiltin()){
                $class = new ReflectionClass($param->getType()->getName());
            }else{
                $class = null;
            }

            if(!is_null($class) && array_key_exists($class->getName(),$this->args)){
                $parameters[] = $this->args[$class->getName()];
            }

            else if (!is_null($class)) {
                $cl = $class->getName();
                if(class_exists($cl)){
                    $parameters[] = new $cl;
                }
            } else {
                
                if (empty($this->args)) {
                    continue;
                }
                $uriParams = array_reverse($this->args);
                $parameters[] = array_pop($this->args);
                $uriParams = array_reverse($this->args);
            }
        }

        return $parameters;
    }
    protected function callable($callback)
    {
        return new ReflectionFunction($callback);
    }

    protected function abc(){
        
    }
    protected function classMethod($callback)
    {
        if(class_exists($callback[0]))
        {
            
            if(isset($callback[1])){
                // $callback[0] = new $callback[0]();
                $callback[0] = new ReflectionClass($callback[0]);
        
                $callback[0]->newInstanceArgs($args);
                if(method_exists($callback[0],$callback[1]))
                {
                    return new ReflectionMethod($callback[0],$callback[1]);
                }else{
                    throw new NotFoundException("Method <b>\"{$callback[1]}\"</b> not found !");
                }
            }else{
         
                return new ReflectionClass($callback[0]);
            }

        }else{
            throw new \NotFoundException("Controller <b>\"{$callback[0]}\"</b> not found !");
        }
    }



    public function call($callback)
    {

        if(is_array($callback))
        {
            if(isset($callback[1])){
                list($class,$method) = $callback;
                $reflect = new ReflectionMethod($class,$method);
                $callback[0] = new ReflectionClass($callback[0]);
                $constructor = $callback[0]->getConstructor();

                if(!empty($constructor)) {
                    
                    $parameters = $constructor->getParameters();
                    $args = $this->generateArgs($callback[0]);
                }
                $callback[0] = $callback[0]->newInstanceArgs($args ?? []);
  
            }
    
            $args = $this->generateArgs($reflect) ?? [];
            return call_user_func_array($callback,$args);

        }elseif(is_callable($callback)){
            $reflect = $this->callable($callback);
            $args = $this->generateArgs($reflect) ?? [];
            return call_user_func_array($callback,$args);
            
        }else{
            throw new \Exception("error");
        }
        
        

        
    }
    
}