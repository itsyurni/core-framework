<?php
namespace yurni\framework\Exception;

class ForbiddenException extends \Exception {
    protected $code = 403;
    public function errorMessage() {
        //error message
        $errorMsg = '<b>'.$this->getMessage().'</b>';
        return $errorMsg;
      }
}