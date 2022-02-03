<?php
namespace yurni\framework\Exception;

class RuntimeException extends \Exception {
    protected $code = 403;
    public function errorMessage() {
        //error message
        $errorMsg = 'Runtime Error : <b>'.$this->getMessage().'</b>';
        return $errorMsg;
      }
}