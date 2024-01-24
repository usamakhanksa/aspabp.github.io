<?php

$ROOT = $_SERVER["DOCUMENT_ROOT"];
require_once $ROOT . '/fatoora/vendor/autoload.php';
require_once $ROOT . "/fatoora/app/database/Db.php";

class InvoiceDetail extends Db
{
    public function findAllByInvoiceRecID($invoiceRecID)
    {
        $query = "SELECT 
                ProductRecID,
                RecordNumber, 
                Barcode, 
                ProductFullName, 
                ProductFullNameAR, 
                OrderQuantity, 
                UnitAmount,
                UnitAmountVAT,
                SubTotal,
                SalesTaxAmount, 
                TotalAmount, 
                UPCTypeRecID
                FROM POS. V_InvoiceDetail 
                WHERE InvoiceRecID = ? and StatusRecID =1";

        $statement = $this->connect()->prepare($query);
        $statement->execute([$invoiceRecID]);
        $result = $statement->fetchAll();

        if ($result > 0) {
            return $result;
        } else {
            return false;
        }
    }

    public function findAllByBusinessInvoiceRecID($invoiceRecID)
    {
        $query = "SELECT
        sod.ProductRecID, 
        sod.SalesOrderDetailRecID AS RecordNumber, 
        p.UPC AS Barcode, 
        p.Description AS ProductFullName, 
        p.DescriptionAR AS ProductFullNameAR, 
        sod.OrderQuantity, 
        sod.UnitAmount, 
        sod.SubTotal, 
        sod.SalesTaxAmount, 
        sod.TotalAmount, 
        p.UPCTypeRecID
        FROM
            Business.V_SalesOrder_Detail AS sod
            INNER JOIN
            Inventory.Product AS p
            ON 
                sod.ProductRecID = p.RecID
        WHERE
        sod.SalesOrderRecID = ? AND
        sod.StatusRecID = 1";

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
        FROM [V_InvoiceDetail] vidt
        INNER JOIN [InvoiceDetail] idt ON vidt.InvoiceRecID = idt.RecID
        WHERE vidt.InvoiceRecID = ? 
        ORDER BY idt.RecID DESC";

        $statement = $this->connect()->prepare($query);
        $statement->execute([$invoiceRecID]);
        $result = $statement->fetchAll();

        if ($result > 0) {
            return $result;
        } else {
            return false;
        }
    }

    public function deleteAllByInvoiceNumber($invoiceNumber)
    {
        $query = "DELETE FROM [InvoiceDetail]
        WHERE [InvoiceDetail].[InvoiceRecID] = 
            (SELECT [RecID] FROM [Invoice] WHERE [InvoiceNumber] = ?)";

        $statement = $this->connect()->prepare($query);
        $statement->execute([$invoiceNumber]);

        return true;
    }
}
