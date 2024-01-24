<?php

$ROOT = $_SERVER["DOCUMENT_ROOT"];
require_once $ROOT . '/fatoora/vendor/autoload.php';

class Db
{
    private $conn;
    private $statement;
    private $query;

    public function connect()
    {
        $servername = 'LAPTOP-UEAR0CO0\SQLEXPRESS01';
        $dbname = 'Emtyaz';
        $username = 'sa';
        $password = 'sa123';

        $connectionOptions = array(
            "Database" => $dbname,
            "Uid" => $username,
            "PWD" => $password,
            "CharacterSet" => "UTF-8",
            "ReturnDatesAsStrings" => true
        );

        $conn = sqlsrv_connect($servername, $connectionOptions);

        if (!$conn) {
            die("Connection failed: " . print_r(sqlsrv_errors(), true));
        }

        $this->conn = $conn;

        return $this;
    }

    public function prepare($query)
    {
        $this->query = $query;
        return $this;
    }

    public function execute($params = [])
    {
        $stmt = sqlsrv_prepare($this->conn, $this->query, $params);
        $this->statement = $stmt;
        $result = sqlsrv_execute($this->statement);

        if ($result) {
            return $result;
        } else {
            $e = sqlsrv_errors();
            var_dump($e);
            die();
        }   
    }

    public function fetch()
    {
        $row = sqlsrv_fetch_array($this->statement, SQLSRV_FETCH_ASSOC);

        return $row;
    }

    public function fetchAll()
    {
        $rows = [];

        while ($row = $this->fetch()) {
            $rows[] = $row;
        }

        return $rows;
    }
}
