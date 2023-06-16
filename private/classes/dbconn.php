<?php

if(!defined('DB_SERVER')){
    require_once("../../initialize.php");
}

class DBConnection
{
    private $host = DB_SERVER;
    private $username = DB_USERNAME;
    private $password = DB_PASSWORD;
    private $database = DB_NAME;

    // connection object
    public $conn;

    // constructor
    public function __construct()
    {
        // if connection object is not already set, set it
        if(!isset($this->conn)){
            $this->conn = new mysqli($this->host, $this->username, $this->password, $this->database);

            if ($this->conn->connect_error) {
                echo "Error connecting to database: " . $this->conn->connect_error;
                exit;
            }
        }
    }

    // destructor
    public function __destruct()
    {
        // close the connection when the object is destroyed
        if ($this->conn) {
            $this->conn->close();
        }
    }
}

?>