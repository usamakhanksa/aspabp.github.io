<?php

$ROOT = $_SERVER["DOCUMENT_ROOT"];
require_once $ROOT . '/fatoora/vendor/autoload.php';
require_once $ROOT . "/fatoora/app/database/Db.php";

class Invoice extends Db
{
    public function findInvoiceRecordsByUser($limit_start = 0, $range = 100, $username)
    {
        $query = "SELECT [Code] CustomerCode
        ,[Name]
        ,[NameAR]
        ,[RecID]
        ,[CustomerRecID]
        ,[PriceTypeRecID]
        ,[InvoiceNumber]
        ,[InvoiceDate]
        ,[CashierPerson]
        ,[GrandTotal]
        FROM [V_Invoice]
        WHERE [CreatedBy] = '" . $username . "' AND (StatusRecID = 1 OR StatusRecID = 2)
        ORDER BY RecID
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

    public function findInvoiceRecordsByUserBySearchTerm($limit_start = 0, $range = 100, $userRecID, $term)
    {
        $query = "SELECT [Code] CustomerCode
        ,[Name]
        ,[NameAR]
        ,[RecID]
        ,[CustomerRecID]
        ,[PriceTypeRecID]
        ,[InvoiceNumber]
        ,[InvoiceDate]
        ,[CashierPerson]
        ,[GrandTotal]
        FROM [V_Invoice]
        WHERE [CreatedBy] = '" . $userRecID . "'
        ORDER BY RecID
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

    public function findInvoiceRecordsOnHoldByUser($username)
    {
        $query = "SELECT [Code] CustomerCode
        ,[Name]
        ,[NameAR]
        ,[RecID]
        ,[CustomerRecID]
        ,[PriceTypeRecID]
        ,[InvoiceNumber]
        ,[InvoiceDate]
        ,[CashierPerson]
        ,[GrandTotal]
        FROM [V_Invoice]
        WHERE [CreatedBy] = ? AND [StatusRecID] = 4
        ORDER BY RecID";

        $statement = $this->connect()->prepare($query);
        $statement->execute([$username]);
        $resultSet = $statement->fetchAll();

        return ($resultSet) ? $resultSet : false;
    }

    public function findInvoiceRecordsOnHoldByUserBySearchTerm($limit_start = 0, $range = 100, $userRecID, $term)
    {
        $query = "SELECT [Code] CustomerCode
        ,[Name]
        ,[NameAR]
        ,[RecID]
        ,[CustomerRecID]
        ,[PriceTypeRecID]
        ,[InvoiceNumber]
        ,[InvoiceDate]
        ,[CashierPerson]
        ,[GrandTotal]
        FROM [V_Invoice]
        WHERE [CreatedBy] = '" . $userRecID . "', [StatusRecID] = 4
        ORDER BY RecID
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

    public function findInvoiceHeaderFooter($recID)
    {
        $query = "SELECT 
        InvoiceNumber, 
        convert (varchar,InvoiceDate) as Date, 
        convert (varchar, CreatedTime, 8) as Time, 
        CashierID, 
        CashAmount, 
        CardAmount, 
        BalanceAmount, 
        TotalSubTotal, 
        TotalVATAmount, 
        GrandTotal, 
        CustomerCode,
        CustomerName, 
        CustomerNameAR, 
        VATNumber, 
        Remarks 
        FROM POS.V_InvoiceHeaderFooter 
        WHERE RecID = ?";

        $statement = $this->connect()->prepare($query);
        $statement->execute([$recID]);
        $resultSet = $statement->fetch();

        if ($resultSet > 0) {
            return $resultSet;
        } else {
            return false;
        }
    }

    public function findInvoiceHeaderFooterByInvoiceNumber($invoiceNumber)
    {
        $query = "SELECT
        POS.V_Invoice.RecID, 
        POS.V_Invoice.InvoiceNumber, 
        CONVERT ( VARCHAR, POS.V_Invoice.InvoiceDate ) AS [DATE], 
        CONVERT ( VARCHAR, POS.V_Invoice.CreatedTime, 8 ) AS [TIME], 
        CONVERT ( VARCHAR, POS.V_Invoice.CollectionDate ) AS DeliveryDate, 
        POS.V_Invoice.TotalSubTotal, 
        POS.V_Invoice.TotalVATAmount, 
        POS.V_Invoice.TotalDiscountAmount, 
        POS.V_Invoice.GrandTotal, 
        POS.Invoice.CollectionAmount,
        POS.Invoice.PendingAmount,
        POS.V_Invoice.CashAmount, 
        POS.V_Invoice.CardAmount, 
        POS.V_Invoice.BalanceAmount, 
        POS.V_Invoice.Code AS CustomerCode, 
        POS.V_Invoice.Name AS CustomerName, 
        POS.V_Invoice.NameAR AS CustomerNameAR, 
        POS.V_Invoice.Remarks, 
        Business.Customer.VATNumber
        FROM
            POS.V_Invoice 
            INNER JOIN POS.Invoice ON POS.V_Invoice.RecID = POS.Invoice.RecID
            LEFT JOIN Business.Customer ON POS.V_Invoice.CustomerRecID = Business.Customer.RecID
        WHERE
        POS.V_Invoice.InvoiceNumber = ?";

        $statement = $this->connect()->prepare($query);
        $statement->execute([$invoiceNumber]);
        $resultSet = $statement->fetch();

        if ($resultSet > 0) {
            return $resultSet;
        } else {
            return false;
        }
    }

    public function findBusinessInvoiceHeaderFooterByInvoiceNumber($invoiceNumber)
    {
        $query = "SELECT
        so.RecID,
        so.InvoiceNumber, 
        so.GrandTotal, 
        so.TotalVATAmount, 
        so.TotalSubTotal, 
        so.TotalDiscountAmount, 
        so.TotalUnitAmount, 
        so.CollectionAmount, 
        so.PendingAmount, 
        so.CashAmount, 
        so.CardAmount, 
        so.BalanceAmount,
        CONVERT ( VARCHAR, so.OrderDate) AS [DATE], 
        CONVERT ( VARCHAR, so.CreatedTime, 8) AS [TIME], 
        CONVERT ( VARCHAR, so.DeliveryDate) AS DeliveryDate, 
        CONVERT ( VARCHAR, so.DeliveryTime) AS DeliveryTime, 
        so.OrderNumber, 
        so.CustomerRecID, 
        so.QuotationNumber, 
        c.Code AS CustomerCode, 
        c.Name AS CustomerName, 
        c.NameAR AS CustomerNameAR, 
        c.VATNumber, 
        c.Remarks
        FROM
            Business.SalesOrder AS so
            INNER JOIN
            Business.Customer AS c
            ON 
                so.CustomerRecID = c.RecID
        WHERE
        so.InvoiceNumber = ?";

        $statement = $this->connect()->prepare($query);
        $statement->execute([$invoiceNumber]);
        $resultSet = $statement->fetch();

        if ($resultSet > 0) {
            return $resultSet;
        } else {
            return false;
        }
    }

    public function findByInvoiceNumber($invoiceNumber)
    {
        $query = "SELECT [Code] CustomerCode
        ,[Name]
        ,[NameAR]
        ,[RecID]
        ,[CustomerRecID]
        ,[PriceTypeRecID]
        ,[InvoiceNumber]
        ,[InvoiceDate]
        ,[CashierPerson]
        ,[GrandTotal]
        FROM [V_Invoice] 
        WHERE [InvoiceNumber] = ?";

        $statement = $this->connect()->prepare($query);
        $statement->execute([$invoiceNumber]);
        $resultSet = $statement->fetch();

        if ($resultSet > 0) {
            return $resultSet;
        } else {
            return false;
        }
    }

    public function generateInvoiceNumber()
    {
        // Query for the last entered invoice number
        $query = "SELECT TOP 1 [InvoiceNumber]
                  FROM [Invoice]
                  ORDER BY [RecID] DESC";
    
        $statement = $this->connect()->prepare($query);
        $statement->execute();
        $lastInvoiceNumber = $statement->fetch()['InvoiceNumber'];
    
        // Extract the integer part of the invoice number using regex
        if (preg_match('/(\d+)/', $lastInvoiceNumber, $matches)) {
            $lastInvoiceNumberInt = (int)$matches[0];
            // Add 1 to the integer part
            $newInvoiceNumberInt = $lastInvoiceNumberInt + 1;
            // Format the new invoice number with leading zeros
            $prefix = 'INV/';
            $formattedInvoiceNumber = $prefix . '00000' . $newInvoiceNumberInt;
            return $formattedInvoiceNumber;
        }
    }

    public function makeInvoiceHold($recID)
    {
        $query = "UPDATE [Invoice]
        SET StatusRecID = 4
        WHERE RecID = ?";

        $statement = $this->connect()->prepare($query);
        $statement->execute([$recID]);

        return $recID;
    }
}
