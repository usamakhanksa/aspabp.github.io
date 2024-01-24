<?php

$ROOT = $_SERVER["DOCUMENT_ROOT"];
require_once $ROOT . '/fatoora/vendor/autoload.php';
require_once $ROOT . "/fatoora/app/database/Db.php";

class Customer extends Db
{
    public function find($recID)
    {
        $query = "SELECT
        [RecID]
        ,[Code]
        ,[Name]
        ,[NameAR]
        ,[Phone]
        FROM [Customer]
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

    public function findByCode($customerCode, $customerNo, $customerName)
    {
        $query = "SELECT * FROM Business.Customer AS c WHERE ((c.Code = ? AND c.Code IS NOT NULL) OR (c.Phone = ? AND c.Phone <> 0) OR( c.Name = ? AND c.Name IS NOT NULL)) AND RecID <> 1";
        $stmt = $this->connect()->prepare($query);
        $stmt->execute([$customerCode, $customerNo, $customerName]);

        $result = $stmt->fetch();

        if ($result > 0) {
            return $result;
        } else {
            return false;
        }
    }

    public function findBySearchTerm($term)
    {
        $query = "SELECT TOP (8) [RecID]
        ,[Code]
        ,[Name]
        ,[NameAR]
        ,[Phone]
        FROM [Customer]
        WHERE 
        [Name] LIKE '%" . $term . "%' OR
        [NameAR] LIKE '%" . $term . "%' OR
        [Phone] LIKE '%" . $term . "%' OR
        [Code] LIKE '%" . $term . "%'
        ";

        $statement = $this->connect()->prepare($query);
        $statement->execute();
        $resultSet = $statement->fetchAll();

        if ($resultSet > 0) {
            return $resultSet;
        } else {
            return false;
        }
    }

    public function findCustomerDetails($customerCode)
    {
        $query = "SELECT
        bca.CustomerRecID, 
        bca.AddressLine, 
        bca.POBox, 
        gci.Name AS CityName,
        gco.Code AS CountryCode, 
        bca.StreetName, 
        bca.BuildingNumber, 
        bc.Name AS BusinessName, 
        bc.VATNumber
        FROM
            Business.CustomerAddress AS bca
            LEFT JOIN
            GlobalSetup.City AS gci
            ON 
                bca.CityRecID = gci.RecID
            LEFT JOIN
            GlobalSetup.Country AS gco
            ON 
                bca.CountryRecID = gco.RecID
            LEFT JOIN
            GlobalSetup.District AS gdi
            ON 
                bca.DistrictRecID = gdi.RecID
            INNER JOIN
            Business.Customer AS bc
            ON 
                bca.CustomerRecID = bc.RecID
        WHERE
        bc.Code = ?";

        $statement = $this->connect()->prepare($query);
        $statement->execute([$customerCode]);
        $resultSet = $statement->fetch();

        if ($resultSet > 0) {
            return $resultSet;
        } else {
            return false;
        }
    }
}
