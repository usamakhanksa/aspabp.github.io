<?php

$ROOT = $_SERVER["DOCUMENT_ROOT"];
require_once $ROOT . '/fatoora/vendor/autoload.php';
require_once $ROOT . "/fatoora/app/database/Db.php";
require_once $ROOT . "/fatoora/app/inventory/Inventory.php";
require_once $ROOT . "/fatoora/app/invoiceTemp/invoiceTemp.php";
require_once $ROOT . "/fatoora/app/invoice/Invoice.php";
require_once $ROOT . "/fatoora/app/utils/productSource.php";

class InvoiceDetailTemp extends Db
{
    public function findById($recId)
    {
        $query = "SELECT [InvoiceDetailRecID] AS RecID
        ,[InvoiceRecID]
        ,[PriceTypeRecID]
        ,[ProductRecID]
        ,[OrderQuantity]
        ,[UnitAmount]
        ,[RetailPrice]
        ,[WholesalePrice]
        ,[TotalAmount]
        ,[ProductSourceRecID]
        FROM [V_InvoiceDetailTemporary]
        WHERE [InvoiceDetailRecID] = ?";

        $statement = $this->connect()->prepare($query);
        $statement->execute([$recId]);
        $resultSet = $statement->fetch();

        if ($resultSet > 0) {
            return $resultSet;
        } else {
            return false;
        }
    }

    public function findAllByInvoiceRecID($invoiceRecID)
    {
        $query = "SELECT [RecID]
                         ,[InvoiceRecID]
                         ,[ProductRecID]
                         ,[UnitAmount]
                         ,[OrderQuantity]
                         ,[PriceTypeRecID]
                         ,[SubTotal]
                         ,[SalesTaxAmount]
                         ,[TotalAmount]
                  FROM [InvoiceDetailTemporary]
                  WHERE [InvoiceRecID] = ?";

        $statement = $this->connect()->prepare($query);
        $statement->execute([$invoiceRecID]);
        $result = $statement->fetchAll();

        if ($result > 0) {
            return $result;
        } else {
            return false;
        }
    }

    public function findAllInventoryByInvoiceRecID($invoiceRecID)
    {
        $query = "SELECT [RecordNumber]
        ,vidt.[InvoiceDetailRecID]
        ,vidt.[InvoiceRecID]
        ,vidt.[Barcode]
        ,vidt.[ProductRecID]
        ,vidt.[ProductFullName]
        ,vidt.[ProductPackageTypeRecID]
        ,vidt.[ProductPackageTypeCodeAR]
        ,vidt.[OrderQuantity]
        ,vidt.[UnitAmount]
        ,vidt.[WholesalePrice]
        ,vidt.[TotalAmount]
        ,idt.[PriceTypeRecID]
        ,idt.[ProductSourceRecID]
        FROM [V_InvoiceDetailTemporary] vidt
        INNER JOIN [InvoiceDetailTemporary] idt ON vidt.InvoiceDetailRecID = idt.RecID
        WHERE vidt.InvoiceRecID = ? 
        ORDER BY idt.RecID";

        $statement = $this->connect()->prepare($query);
        $statement->execute([$invoiceRecID]);
        $result = $statement->fetchAll();

        if ($result > 0) {
            return $result;
        } else {
            return false;
        }
    }

    public function calculateTotalAmount($unitAmount, $orderQuantity)
    {
        return $unitAmount * $orderQuantity;
    }

    public function create($invoiceTempId, $dic)
    {
        // $totalAmount = $this->calculateTotalAmount($dic['UnitAmount'], $dic['OrderQuantity']);

        $query = "INSERT INTO [InvoiceDetailTemporary]
        ([InvoiceRecID]
        ,[ProductRecID]
        ,[UnitAmount]
        ,[OrderQuantity]
        ,[PriceTypeRecID]
        ,[ProductSourceRecID])
        VALUES (?, ?, ?, ?, ?, ?)";
        $statement = $this->connect()->prepare($query);
        $statement->execute([$invoiceTempId, $dic['ProductRecID'], $dic['UnitAmount'], $dic['OrderQuantity'], $dic['PriceTypeRecID'], $dic['ProductSourceRecID']]);

        $invoiceTemp = (new InvoiceTemp())->calculateTotalsAndUpdate($invoiceTempId);
        return $invoiceTemp;
    }

    public function updatePriceType($recId, $priceTypeRecID)
    {
        // Check if the record exists
        $existingRecord = $this->findById($recId);
        if ($existingRecord === false) {
            return false; // Record not found
        }

        if ($priceTypeRecID == 2) {
            $unitAmount = $existingRecord['WholesalePrice'];
        } else {
            $unitAmount = $existingRecord['RetailPrice'];
        }

        $query = "UPDATE [InvoiceDetailTemporary]
                  SET [PriceTypeRecID] = ?, [UnitAmount] = ?
                    OUTPUT Inserted.[InvoiceRecID]
                  WHERE [RecID] = ?";

        $statement = $this->connect()->prepare($query);
        $statement->execute([$priceTypeRecID, $unitAmount, $recId]);
        $result = $statement->fetch();

        $invoiceTemp = (new InvoiceTemp())->calculateTotalsAndUpdate($result['InvoiceRecID']);
        return $invoiceTemp;
    }

    public function updateQuantity($recId, $newQuantity)
    {
        $record = $this->findById($recId);
        $totalCheckedModes = 0;
        $mode = getModeByProductSourceRecID($record['ProductSourceRecID']);
        $hasStock = (new Inventory())->hasEnoughStock($record['ProductRecID'], $newQuantity, $mode);


        if (isset($hasStock) && $hasStock['status'] == false) {
            // update the quantity to current stock
            $currentStock = $hasStock['StockOnHand'];
            $this->updateQuantity($recId, $currentStock);
            $newQuantity = $newQuantity - $currentStock;

            if ($totalCheckedModes < 1) {
                $totalCheckedModes += 1;
                $mode = getModeByProductSourceRecID(getNextProductSourceRecIDByMode($mode));
                $hasStock = (new Inventory())->hasEnoughStock($record['ProductRecID'], $newQuantity, $mode);
                $productSourceRecID = getProductSourceRecIDByMode($mode);
                $existingRecord = $this->findRecordByProductRecID($record['InvoiceRecID'], $record['ProductRecID'], $mode);

                if (!$hasStock['status'] || $existingRecord) {
                    header('Content-type: application/json');
                    echo json_encode(['status' => 'unsuccess', 'type' => 'no-stock']);
                    exit();
                }

                $dic = ['ProductRecID' => $record['ProductRecID'], 'UnitAmount' => $record['WholesalePrice'], 'OrderQuantity' => $newQuantity, 'PriceTypeRecID' => 2, 'ProductSourceRecID' => getProductSourceRecIDByMode($mode)];
                return $this->create($record['InvoiceRecID'], $dic);
            }

            header('Content-type: application/json');
            echo json_encode(['status' => 'unsuccess', 'type' => 'no-stock']);
            exit();
        } else if (!isset($hasStock)) {
            header('Content-type: application/json');
            echo json_encode(['status' => 'unsuccess', 'type' => 'no-stock']);
            exit();
        }

        $query = "UPDATE [InvoiceDetailTemporary]
                  SET [OrderQuantity] = ?
                  OUTPUT Inserted.[InvoiceRecID]
                  WHERE [RecID] = ?";

        $statement = $this->connect()->prepare($query);
        $statement->execute([$newQuantity, $recId]);
        $result = $statement->fetch();

        $invoiceTemp = (new InvoiceTemp())->calculateTotalsAndUpdate($result['InvoiceRecID']);
        return $invoiceTemp;
    }

    public function delete($recId)
    {
        // Check if the record exists
        $existingRecord = $this->findById($recId);
        if ($existingRecord === false) {
            return false; // Record not found
        }

        // Delete the record
        $query = "DELETE FROM [InvoiceDetailTemporary] 
        OUTPUT Deleted.[InvoiceRecID] 
        WHERE [RecID] = ?";

        $statement = $this->connect()->prepare($query);
        $statement->execute([$recId]);
        $result = $statement->fetch();

        $invoiceTemp = (new InvoiceTemp())->calculateTotalsAndUpdate($result['InvoiceRecID']);
        return $invoiceTemp;
    }

    public function addByBarcode($invoiceRecID, $barcode, $mode)
    {
        $product = (new Inventory())->findInventoryRecordsByBarcode($barcode, $mode);

        if ($product == false) {
            header('Content-type: application/json');
            echo json_encode(['status' => 'unsuccess', 'type' => 'no-product']);
            exit();
        }

        $hasStock = (new Inventory())->hasEnoughStock($product['RecID'], 1, $mode);

        if (is_array($hasStock)) {
            if ($hasStock['status'] == false) {
                header('Content-type: application/json');
                echo json_encode(['status' => 'unsuccess', 'type' => 'no-stock']);
                exit();
            }
        } else {
            if ($hasStock == false) {
                header('Content-type: application/json');
                echo json_encode(['status' => 'unsuccess', 'type' => 'no-stock']);
                exit();
            }
        }

        if ($product) {
            // Check if a record with the same ProductRecID already exists
            $existingRecord = $this->findRecordByProductRecID($invoiceRecID, $product['RecID'], $mode);

            if ($existingRecord) {
                // If record already exists, update the quantity
                $newQuantity = $existingRecord['OrderQuantity'] + 1;
                return $this->updateQuantity($existingRecord['RecID'], $newQuantity);
            } else {
                // If no record exists, create a new one
                $dic = ['ProductRecID' => $product['RecID'], 'UnitAmount' => $product['WholesalePrice'], 'OrderQuantity' => 1, 'PriceTypeRecID' => 2, 'ProductSourceRecID' => getProductSourceRecIDByMode($mode)];
                return $this->create($invoiceRecID, $dic);
            }
        }

        return false;
    }

    public function addWeightedByBarcode($invoiceRecID, $barcode, $mode, $quantity)
    {
        $product = (new Inventory())->findInventoryRecordsByBarcode($barcode, $mode);

        if ($product == false) {
            header('Content-type: application/json');
            echo json_encode(['status' => 'unsuccess', 'type' => 'no-product']);
            exit();
        }
        
        $hasStock = (new Inventory())->hasEnoughStock($product['RecID'], $quantity, $mode);

        if (isset($hasStock) && $hasStock['status'] == false) {
            header('Content-type: application/json');
            echo json_encode(['status' => 'unsuccess', 'type' => 'no-stock']);
            exit();
        } else if (!isset($hasStock)) {
            header('Content-type: application/json');
            echo json_encode(['status' => 'unsuccess', 'type' => 'no-stock']);
            exit();
        }

        if ($product) {
            // Check if a record with the same ProductRecID already exists
            $existingRecord = $this->findRecordByProductRecID($invoiceRecID, $product['RecID'], $mode);

            if ($existingRecord) {
                // If record already exists, update the quantity
                $newQuantity = (float)$existingRecord['OrderQuantity'] + $quantity;
                return $this->updateQuantity($existingRecord['RecID'], $newQuantity);
            } else {
                // If no record exists, create a new one
                $dic = ['ProductRecID' => $product['RecID'], 'UnitAmount' => $product['WholesalePrice'], 'OrderQuantity' => $quantity, 'PriceTypeRecID' => 2, 'ProductSourceRecID' => getProductSourceRecIDByMode($mode)];
                return $this->create($invoiceRecID, $dic);
            }
        }

        return false;
    }

    private function findRecordByProductRecID($invoiceRecID, $productRecID, $mode)
    {
        $productSourceRecID = getProductSourceRecIDByMode($mode);
        $query = "SELECT [RecID], [OrderQuantity]
                  FROM [InvoiceDetailTemporary]
                  WHERE [InvoiceRecID] = ? AND [ProductRecID] = ? AND [ProductSourceRecID] = ?";

        $statement = $this->connect()->prepare($query);
        $statement->execute([$invoiceRecID, $productRecID, $productSourceRecID]);
        $result = $statement->fetch();

        return $result;
    }

    public function insertInvoiceDetailTempToInvoiceDetailByInvoiceRecId($invoiceTempRecID, $invoiceRecID)
    {
        $queryInvoiceDetail = "INSERT INTO [InvoiceDetail] (
            [InvoiceRecID],
            [PriceTypeRecID],
            [ProductRecID],
            [OrderQuantity],
            [UnitAmount],
            [DiscountPercentage],
            [DiscountAmount],
            [TotalDiscountAmount],
            [SalesTaxRecID],
            [StatusRecID],
            [Reference],
            [CreatedBy],
            [CreatedDate],
            [CreatedBranchRecID],
            [ModifiedBy],
            [ModifiedDate],
            [ProductSourceRecID]
        )
        SELECT
            $invoiceRecID,
            [PriceTypeRecID],
            [ProductRecID],
            [OrderQuantity],
            [UnitAmount],
            [DiscountPercentage],
            [DiscountAmount],
            [TotalDiscountAmount],
            [SalesTaxRecID],
            [StatusRecID],
            [Reference],
            [CreatedBy],
            [CreatedDate],
            [CreatedBranchRecID],
            [ModifiedBy],
            [ModifiedDate],
            [ProductSourceRecID]
        FROM [InvoiceDetailTemporary]
        WHERE [InvoiceRecID] = '" . $invoiceTempRecID . "'";

        $statement = $this->connect()->prepare($queryInvoiceDetail);
        $statement->execute();
        return true;
    }

    public function insertInvoiceDetailToInvoiceDetailTempByInvoiceTempRecId($invoiceRecID, $invoiceTempRecID)
    {
        $queryInvoiceDetail = "INSERT INTO [InvoiceDetailTemporary] (
            [InvoiceRecID],
            [PriceTypeRecID],
            [ProductRecID],
            [OrderQuantity],
            [UnitAmount],
            [DiscountPercentage],
            [DiscountAmount],
            [TotalDiscountAmount],
            [SalesTaxRecID],
            [StatusRecID],
            [Reference],
            [CreatedBy],
            [CreatedDate],
            [CreatedBranchRecID],
            [ModifiedBy],
            [ModifiedDate],
            [ProductSourceRecID]
        )
        SELECT
            $invoiceTempRecID,
            [PriceTypeRecID],
            [ProductRecID],
            [OrderQuantity],
            [UnitAmount],
            [DiscountPercentage],
            [DiscountAmount],
            [TotalDiscountAmount],
            [SalesTaxRecID],
            [StatusRecID],
            [Reference],
            [CreatedBy],
            [CreatedDate],
            [CreatedBranchRecID],
            [ModifiedBy],
            [ModifiedDate],
            [ProductSourceRecID]
        FROM [InvoiceDetail]
        WHERE [InvoiceRecID] = '" . $invoiceRecID . "'";

        $statement = $this->connect()->prepare($queryInvoiceDetail);
        $statement->execute();
        return true;
    }
}
