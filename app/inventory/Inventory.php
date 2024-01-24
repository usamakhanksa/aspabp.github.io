<?php

$ROOT = $_SERVER["DOCUMENT_ROOT"];
require_once $ROOT . '/fatoora/vendor/autoload.php';
require_once $ROOT . "/fatoora/app/database/Db.php";
require_once $ROOT . "/fatoora/app/constants/Constants.php";
require_once $ROOT . "/fatoora/app/invoiceDetailTemp/InvoiceDetailTemp.php";

class Inventory extends Db
{
    public function findInventoryRecords($limit_start = 0, $range = 100, $mode = InventoryModes::WAREHOUSE, $productTypeRecID)
    {
        $table = getTableNameByMode($mode);
        $recID_columnName = getRecIDColumnName($mode);
        $condition = $productTypeRecID ? "WHERE [ProductTypeRecID] = $productTypeRecID" : '';
        $query = "SELECT [Warehouse]
        ,$recID_columnName AS RecID
        ,[UPC]
        ,[SKU]
        ,[Description] AS ProductName
        ,[DescriptionAR] AS ProductNameAR
        ,[WholesalePrice]
        ,[RetailPrice]
        ,[ProductPackageTypeCode]
        ,[ProductPackageTypeCodeAR]
        ,[StockOnHand]
        FROM $table 
        $condition
        ORDER BY $recID_columnName
        OFFSET " . $limit_start . " ROWS
        FETCH NEXT " . ($range) . " ROWS ONLY";

        $statement = $this->connect()->prepare($query);
        $statement->execute();
        $resultSet = $statement->fetchAll();

        if ($resultSet > 0) {
            return $resultSet;
        } else {
            return false;
        }
    }

    public function findInventoryRecordsBySearchTerm($searchTerm, $limit_start = 0, $range = 100, $mode = InventoryModes::WAREHOUSE)
    {
        $table = getTableNameByMode($mode);
        $recID_columnName = getRecIDColumnName($mode);
        $query = "SELECT [Warehouse]
        ,$recID_columnName AS RecID
        ,[UPC]
        ,[SKU]
        ,[Description] AS ProductName
        ,[DescriptionAR] AS ProductNameAR
        ,[WholesalePrice]
        ,[RetailPrice]
        ,[ProductPackageTypeCode]
        ,[ProductPackageTypeCodeAR]
        ,[StockOnHand]
        ,[UnitProductRecID]
        FROM $table
        WHERE 
        [UnitProductRecID] IN (SELECT 
            UnitProductRecID 
            FROM $table 
            WHERE
            [ProductName] LIKE '%" . $searchTerm . "%' OR
            [ProductNameAR] LIKE '%" . $searchTerm . "%' OR
            [Description] LIKE '%" . $searchTerm . "%' OR
            [DescriptionAR] LIKE '%" . $searchTerm . "%' OR
            [SKU] LIKE '%" . $searchTerm . "%' OR
            [UPC] LIKE '%" . $searchTerm . "%')
        ORDER BY $recID_columnName
        OFFSET " . $limit_start . " ROWS
        FETCH NEXT " . ($range) . " ROWS ONLY";

        $statement = $this->connect()->prepare($query);
        $statement->execute();
        $resultSet = $statement->fetchAll();

        if ($resultSet > 0) {
            return $resultSet;
        } else {
            return false;
        }
    }

    public function findInventoryRecordsByBarcode($barcode, $mode = InventoryModes::WAREHOUSE)
    {
        $table = getTableNameByMode($mode);
        $recID_columnName = getRecIDColumnName($mode);
        $query = "SELECT [Warehouse]
        ,$recID_columnName AS RecID
        ,[UPC]
        ,[SKU]
        ,[ProductName]
        ,[ProductNameAR]
        ,[WholesalePrice]
        ,[RetailPrice]
        ,[ProductPackageTypeCode]
        ,[ProductPackageTypeCodeAR]
        ,[StockOnHand]
        FROM $table
        WHERE [UPC] LIKE '" . $barcode . "' ";
        $statement = $this->connect()->prepare($query);
        $statement->execute();
        $resultSet = $statement->fetch();

        if ($resultSet > 0) {
            return $resultSet;
        } else {
            return false;
        }
    }

    public function findInventoryRecordsByRecID($recID, $mode = InventoryModes::WAREHOUSE)
    {
        $table = getTableNameByMode($mode);
        $recID_columnName = getRecIDColumnName($mode);
        $query = "SELECT [Warehouse]
        ,$recID_columnName AS RecID
        ,[UPC]
        ,[SKU]
        ,[ProductName]
        ,[ProductNameAR]
        ,[WholesalePrice]
        ,[RetailPrice]
        ,[ProductPackageTypeCode]
        ,[ProductPackageTypeCodeAR]
        ,[StockOnHand]
        FROM $table
        WHERE $recID_columnName = ?";

        $statement = $this->connect()->prepare($query);
        $statement->execute([$recID]);
        $resultSet = $statement->fetch();

        if ($resultSet > 0) {
            return $resultSet;
        } else {
            return false;
        }
    }

    public function findProductTypes()
    {
        $query = "SELECT [RecID]
        ,[ProductGroupRecID]
        ,[Code]
        ,[Name]
        ,[NameAR]
        FROM [ProductType]";

        $statement = $this->connect()->prepare($query);
        $statement->execute();
        $resultSet = $statement->fetchAll();

        if ($resultSet > 0) {
            return $resultSet;
        } else {
            return false;
        }
    }

    public function hasEnoughStock($productRecID, $quantity, $mode)
    {
        $table = getTableNameByMode($mode);
        $recID_columnName = getRecIDColumnName($mode);

        // Query the database to get the current stock quantity for the product with the given RecID.
        $query = "SELECT [StockOnHand], [SalableQuantityMaximum], [WholesalePrice], [CostPrice] FROM $table WHERE $recID_columnName = ?";
        $statement = $this->connect()->prepare($query);
        $statement->execute([$productRecID]);
        $row = $statement->fetch();

        if ($row && isset($row['StockOnHand'])) {
            $currentStock = $row['StockOnHand'];
            $sellableMax = $row['SalableQuantityMaximum'];
            $costPrice = $row['CostPrice'];
            $wholesalePrice = $row['WholesalePrice'];

            // check if the quantity is under sellabla quantity maximum
            if ($wholesalePrice < $costPrice) {
                header('Content-type: application/json');
                echo json_encode(['status' => 'unsuccess', 'type' => 'higher-cost-price']);
                exit();
            } else if ($quantity > $sellableMax && $sellableMax != 0) {
                header('Content-type: application/json');
                echo json_encode(['status' => 'unsuccess', 'type' => 'exceeds-sellable-max']);
                exit();
                // Check if there is enough stock.
            } else if ($currentStock >= $quantity) {
                return ['status' => true]; // There is enough stock.
            } else {
                return ['status' => false, 'StockOnHand' => $currentStock];
            }
        } else if ($row) {
            return ['status' => false];
        }

        return false; // Not enough stock.
    }

    public function findSubstituteProductsByBarcode($barcode, $mode = InventoryModes::WAREHOUSE)
    {
        $table = getTableNameByMode($mode);
        $recID_columnName = getRecIDColumnName($mode);
        $query = "SELECT DISTINCT
        psd.ProductSubstituteRecID, 
        priw.$recID_columnName AS RecID,
        priw.Warehouse,
        priw.UPC, 
        priw.SKU, 
        priw.Description AS ProductName, 
        priw.DescriptionAR AS ProductNameAR, 
        priw.WholesalePrice, 
        priw.RetailPrice, 
        priw.ProductPackageTypeCode, 
        priw.ProductPackageTypeCodeAR,
        priw.ProductTypeRecID,
		priw.ProductCategoryRecID,
        priw.StockOnHand
        FROM
            $table AS priw
            INNER JOIN
            Inventory.ProductSubstituteDetail AS psd
            ON 
                priw.$recID_columnName = psd.ProductRecID
        WHERE psd.ProductSubstituteRecID IN 
        ((SELECT
            psd.ProductSubstituteRecID
            FROM Inventory.ProductSubstituteDetail AS psd
            INNER JOIN    
            Inventory.Product AS p
            ON psd.ProductRecID = p.RecID
            WHERE p.UPC = ?)) 
        AND
        priw.UPC <> ?
        AND
        priw.StockOnHand >= 1";

        $statement = $this->connect()->prepare($query);
        $statement->execute([$barcode, $barcode]);
        $resultSet = $statement->fetchAll();

        if ($resultSet > 0) {
            return $resultSet;
        } else {
            return false;
        }
    }

    public function findSubstituteProductsByInvoiceDetailTempRecID($invoiceDetailTempRecID, $mode = InventoryModes::WAREHOUSE)
    {
        $table = getTableNameByMode($mode);
        $recID_columnName = getRecIDColumnName($mode);
        $query = "SELECT DISTINCT
        psd.ProductSubstituteRecID, 
        priw.$recID_columnName AS RecID,
        priw.Warehouse,
        priw.UPC, 
        priw.SKU, 
        priw.Description AS ProductName, 
        priw.DescriptionAR AS ProductNameAR,  
        priw.WholesalePrice, 
        priw.RetailPrice, 
        priw.ProductPackageTypeCode, 
        priw.ProductPackageTypeCodeAR,
        priw.ProductTypeRecID,
		priw.ProductCategoryRecID,
        priw.StockOnHand
        FROM
            $table AS priw
            INNER JOIN
            Inventory.ProductSubstituteDetail AS psd
            ON priw.$recID_columnName = psd.ProductRecID
        WHERE
            psd.ProductSubstituteRecID IN 
            ((SELECT
                    psd.ProductSubstituteRecID
                    FROM Inventory.ProductSubstituteDetail AS psd
                    INNER JOIN
                    Inventory.Product AS p
                    ON psd.ProductRecID = p.RecID
                    WHERE
                        p.RecID = (SELECT
                                    idt.ProductRecID
                                    FROM POS.InvoiceDetailTemporary AS idt
                                    WHERE idt.RecID = ?)
                )) 
            AND
            priw.$recID_columnName <> (SELECT
                            idt.ProductRecID
                            FROM POS.InvoiceDetailTemporary AS idt
                            WHERE idt.RecID = ?)
            AND
            priw.StockOnHand >= 1";

        $statement = $this->connect()->prepare($query);
        $statement->execute([$invoiceDetailTempRecID, $invoiceDetailTempRecID]);
        $resultSet = $statement->fetchAll();

        if ($resultSet > 0) {
            return $resultSet;
        } else {
            return false;
        }
    }

    public function processWeightedBarcode($barcode)
    {
        // Extract the unique code (5 characters)
        $uniqueCode = substr($barcode, 0, 7);

        // Extract the weight (remaining characters)
        $weight = substr($barcode, 7);

        // Ensure that the weight has at least 4 characters
        if (strlen($weight) >= 4) {
            // Convert the weight to grams (assuming it's in grams)
            $weightInGrams = intval($weight);
            $quantity = $weightInGrams / 1000;

            // Now, you have the unique code and weight in grams, and you can process them as needed
            $array = [
                'barcode' => $uniqueCode,
                'quantity' => $quantity,
            ];
            return $array;
        } else {
            $array = [
                'barcode' => $uniqueCode,
                'quantity' => 0.00,
            ];
            return $array;
        }
    }

    public function checkWeightedBarcode($barcode)
    {
        if (strlen($barcode) >= 7 && substr($barcode, 0, 2) === "WE") {
            return true;
        } else {
            return false;
        }
    }
}
