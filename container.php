<?php
namespace yurni\framework;
use Reflector;
use ReflectionClass;
use ReflectionParameter;
use ReflectionMethod;
use ReflectionFunction;
class container {
    protected $callback;
    protected array $pm = [];
    public function setParam($pm){
        $this->pm = $pm;
        return $this;
    }
    public function generateParameters(Reflector $reflection)
    {
        $parameters = [];
        foreach ($reflection->getParameters() as $key => $param) {
            $class = $param->getType() && !$param->getType()->isBuiltin() ? new ReflectionClass($param->getType()->getName())
                : null;
            if (!is_null($class)) {
                $cl = $class->getName();
                if(class_exists($cl)){
                    $parameters[] = new $cl;
                }
            } else {
                
                if (empty($this->pm)) {
                    continue;
                }
                $uriParams = array_reverse($this->pm);
                $parameters[] = array_pop($this->pm);
                $uriParams = array_reverse($this->pm);
            }
        }

        return $parameters;
    }
    protected function callable(){
        return new ReflectionFunction($this->callback);
    }
    protected function classMethod(){
        if(class_exists($this->callback[0]))
        {
            $this->callback[0] = new $this->callback[0]();
            if(method_exists($this->callback[0],$this->callback[1]))
            {
                return new ReflectionMethod($this->callback[0],$this->callback[1]);
            }else{
                throw new NotFoundException("Method <b>\"{$this->callback[1]}\"</b> not found !");
            }
        }else{
            throw new NotFoundException("Controller <b>\"{$this->callback[0]}\"</b> not found !");
        }
    }
    protected function getTypeCallback(){
        if(is_array($this->callback))
        {
            return $this->classMethod();
        }elseif(is_callable($this->callback))
        {
            return $this->callable();
    
        }else{
            throw new Exception("error");
        }


    }
    public function get($callback){
        $this->callback = $callback;
        $callback = $this->getTypeCallback();
        $pm = $this->generateParameters($callback) ?? [];
        return call_user_func_array($this->callback, $pm);
    }
}