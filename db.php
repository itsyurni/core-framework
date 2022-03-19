<?php
namespace yurni\framework;
use PDO;

class db {

    private PDO $pdo;

    public function __construct(){
        
        try{
            
            $this->pdo = new PDO("mysql:host=".$_ENV["db_host"].";dbname=".$_ENV["db_name"]."", $_ENV["db_user"], $_ENV["db_pass"]);

        }catch(\PDOException $e){
            throw new \PDOException($e->getMessage(),$e->getCode());
        }
    }

    public function __call(string $name,array $args){
        
        return call_user_func_array([$this->pdo,$name],$args);
    }
}