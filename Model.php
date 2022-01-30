<?php
namespace yurni\framework;

class Model
{
	public $connection;
	public function __construct(){
		$dbhost = 'localhost'; $dbuser = 'root'; $dbpass = ''; $dbname = ''; $charset = 'utf8';
		$this->connection = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
		if ($this->connection->connect_error) {
			$this->error('Failed to connect to MySQL - ' . $this->connection->connect_error);
		}
		$this->connection->set_charset($charset);
	}
}
