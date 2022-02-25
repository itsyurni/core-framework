<?php
namespace yurni\framework;

Abstract class Model
{

	public static function db(): db {
        return new db();
    }
}
