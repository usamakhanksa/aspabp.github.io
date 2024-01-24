<?php

$ROOT = $_SERVER["DOCUMENT_ROOT"];
require_once $ROOT . '/fatoora/vendor/autoload.php';
require_once $ROOT . "/fatoora/app/database/Db.php";

class CSID extends Db
{
    protected $table;

    public function processAndSaveResponse($responseData)
    {
        $data = json_decode($responseData, true);

        // Extracting individual data elements from the decoded JSON
        $requestID = $data['requestID'];
        $binarySecurityToken = $data['binarySecurityToken'];
        $secret = $data['secret'];

        // Getting current date
        $currentDate = date('Y-m-d H:i:s');
        // Calculating 7 days from now
        $expireDate = date('Y-m-d H:i:s', strtotime('+7 days'));

        $this->create($requestID, $binarySecurityToken, $secret, $currentDate, $expireDate);
        return true;
    }

    public function find($recID)
    {
        $query = "SELECT 
            [RecID], 
            [Secret], 
            [BinarySecurityToken], 
            [CreatedDate], 
            [ExpireDate], 
            [RequestID]
        FROM $this->table
        WHERE [RecID] = '" . $recID . "'";

        $statement = $this->connect()->prepare($query);
        $statement->execute();
        $resultSet = $statement->fetch();

        if ($resultSet > 0) {
            return $resultSet;
        } else {
            return false;
        }
    }

    public function findLastRecord($table = 'Emtyaz.Fatoora.CSIDTemp')
    {
        $query = "SELECT 
            TOP 1 [RecID], 
            [Secret], 
            [BinarySecurityToken], 
            [CreatedDate], 
            [ExpireDate], 
            [RequestID]
        FROM $this->table
        ORDER BY [RecID] DESC";

        $statement = $this->connect()->prepare($query);
        $statement->execute();
        $resultSet = $statement->fetch();

        if ($resultSet !== false) {
            return $resultSet;
        } else {
            return false;
        }
    }

    public function create($requestID, $binarySecurityToken, $secret, $createdDate, $expireDate)
    {
        $query = "INSERT INTO $this->table
            ([Secret], 
            [BinarySecurityToken], 
            [CreatedDate], 
            [ExpireDate], 
            [RequestID])
            VALUES (?, ?, ?, ?, ?)";
        $statement = $this->connect()->prepare($query);
        $statement->execute([$secret, $binarySecurityToken, $createdDate, $expireDate, $requestID]);
        return true;
    }

    public function update($recID, $requestID, $binarySecurityToken, $secret, $createdDate, $expireDate)
    {
        $query = "UPDATE $this->table SET 
        [Secret] = ?, 
        [BinarySecurityToken] = ?, 
        [CreatedDate] = ?, 
        [ExpireDate] = ?, 
        [RequestID] = ?
        WHERE [RecID] = ?";

        $statement = $this->connect()->prepare($query);
        $statement->execute([$secret, $binarySecurityToken, $createdDate, $expireDate, $requestID, $recID]);

        return true;
    }
}
