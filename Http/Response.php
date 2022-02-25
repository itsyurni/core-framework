<?php
namespace yurni\framework\Http;
use yurni\framework\Application;
use yurni\framework\Router\Router;
class Response  {
    
    protected static $CONTENT_TYPE_HTML = "text/html";
    protected static $CONTENT_TYPE_JSON = "application/json";
    protected static $HEADER_CONTENT_TYPE = "Content-Type";
    protected array $header;
    public $body;
    
    public function __construct(){
        $this->body = null;
        $this->reset();
    }

    public function setStatusCode(int $code){
        http_response_code($code);
        return $this;
    }

    public function getStatusCode(){
        return http_response_code() ?? null;
    }

    public function setHeader($type,$val){
        header($type.':'.$val);
        return $this;
    }

    public function setContentType($val)
    {
        return $this->setHeader(self::$HEADER_CONTENT_TYPE,$val);
    }

    public function json(array $data = [], int $status = 200)
    {
        $json = json_encode($data);
        $this->body = $json;
        $this->setContentType(self::$CONTENT_TYPE_JSON)
        ->setStatusCode($status);
        return $this;
    }

    public function html($content = "", int $status = 200)
    {
        $this->setStatusCode($status)->setContentType(self::$CONTENT_TYPE_HTML);
        $this->body = $content;
        return $this;
    }

    public function redirect($url)
    {
        return $this->setHeader("Location", $url);
    }
    
    public function reset(){
        $this->body = null;
        return $this;
    }
}