<?php
namespace M2U\Models\DB;

use mysqli;

class Connect
{

    public $servername, $dbname, $dbprefix, $username, $password, $conn;

    function __construct()
    {
        $this->servername = DB_SERVER;
        $this->dbname = DB_NAME;
        $this->dbprefix = DB_PREFIX;
        $this->username = DB_USERNAME;
        $this->password = DB_PASSWORD;
        
        $this->conn = new mysqli($this->servername, $this->username, $this->password, $this->dbname, DB_PORT);

        if ($this->conn->connect_error) {
            die("<h1>FAILED ESTABLISHING DATABASE CONNECTION: " . $this->conn->connect_error . '</h1>');
        }
    }
}
