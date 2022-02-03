<?php
namespace yurni\framework\Exception;

class NotFoundException extends \Exception{
    protected $code = 404;
    public function errorMessage() {
        //error message
        $errorMsg = 'Error : '.$this->getMessage().'';
        return $errorMsg;
      }
}