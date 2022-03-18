<?php

namespace yurni\framework;
use yurni\framework\db;
Abstract class Model
{
    public db $db;
    public function __construct(){
        $this->db = new db;
    }
}