<?php

$ROOT = $_SERVER["DOCUMENT_ROOT"];
require_once $ROOT . '/fatoora/vendor/autoload.php';
require_once $ROOT . "/fatoora/app/database/Db.php";
require_once $ROOT . "/fatoora/app/invoice/Invoice.php";
require_once $ROOT . "/fatoora/app/invoiceDetail/InvoiceDetail.php";
require_once $ROOT . "/fatoora/app/invoiceDetailTemp/InvoiceDetailTemp.php";
require_once $ROOT . "/fatoora/app/constants/Constants.php";


class InvoiceTemp extends Db
{
    public function findById($recId)
    {
        $query = "SELECT
        [RecID]
        ,[InvoiceNumber]
        ,[CustomerRecID]
        ,[PriceTypeRecID]
        ,[TotalSubTotal]
        ,[TotalVATAmount]
        ,[GrandTotal]
        ,[BalanceAmount]
        FROM [InvoiceTemporary]
        WHERE [RecID] = '" . $recId . "'";

        $statement = $this->connect()->prepare($query);
        $statement->execute();
        $resultSet = $statement->fetch();

        if ($resultSet > 0) {
            return $resultSet;
        } else {
            return false;
        }
    }

    public function findByInvoiceNumber($invoiceNumber)
    {
        $query = "SELECT
        [RecID]
        ,[CustomerRecID]
        ,[PriceTypeRecID]
        ,[TotalSubTotal]
        ,[TotalVATAmount]
        ,[GrandTotal]
        ,[BalanceAmount]
        FROM [InvoiceTemporary]
        WHERE [InvoiceNumber] = '" . $invoiceNumber . "'";

        $statement = $this->connect()->prepare($query);
        $statement->execute();
        $resultSet = $statement->fetch();

        if ($resultSet > 0) {
            return $resultSet;
        } else {
            return false;
        }
    }

    public function findAllFieldsById($recId)
    {
        $query = "SELECT [RecID]
        ,[CustomerCode]
        ,[CustomerRecID]
        ,[PriceTypeRecID]
        ,[InvoiceNumber]
        ,[InvoiceDate]
        ,[PaymentTermRecID]
        ,[PaymentMethodRecID]
        ,[CashierPerson]
        ,[SalesPerson]
        ,[TotalUnitAmount]
        ,[TotalDiscountAmount]
        ,[TotalSubTotal]
        ,[TotalVATAmount]
        ,[GrandTotal]
        ,[BalanceAmount]
        ,[CardAmount]
        ,[CashAmount]
        ,[Remarks]
        ,[StatusRecID]
        ,[CreatedBranchRecID]
        ,[CreatedBy]
        ,[CreatedDate]
        ,[CreatedTime]
        ,[ModifiedBy]
        ,[ModifiedDate]
        ,[ModifiedTime]
        ,[CollectionBy]
        ,[CollectionDate]
        ,[CollectionTime]
        FROM [InvoiceTemporary]
        WHERE [RecID] = '" . $recId . "'";

        $statement = $this->connect()->prepare($query);
        $statement->execute();
        $resultSet = $statement->fetch();

        if ($resultSet > 0) {
            return $resultSet;
        } else {
            return false;
        }
    }

    public function create($username)
    {
        $query = "INSERT INTO [InvoiceTemporary] ([CreatedBy]) OUTPUT Inserted.RecID VALUES (?)";

        $statement = $this->connect()->prepare($query);
        $statement->execute([$username]);
        $resultSet = $statement->fetch();

        if ($resultSet > 0) {
            return $resultSet;
        } else {
            return false;
        }
    }

    public function updateTotals($recId, $totalSubTotal, $totalVATAmount, $grandTotal, $balanceAmount)
    {
        $query = "UPDATE [InvoiceTemporary]
              SET [TotalSubTotal] = ?,
                  [TotalVATAmount] = ?,
                  [GrandTotal] = ?,
                  [BalanceAmount] = ?
              WHERE [RecID] = ?";

        $statement = $this->connect()->prepare($query);
        $result = $statement->execute([$totalSubTotal, $totalVATAmount, $grandTotal, $balanceAmount, $recId]);

        return $result;
    }

    public function updateInvoiceNumber($recId, $invoiceNumber)
    {
        $query = "UPDATE [InvoiceTemporary]
              SET [InvoiceNumber] = ?
              WHERE [RecID] = ?";

        $statement = $this->connect()->prepare($query);
        $result = $statement->execute([$invoiceNumber, $recId]);

        return $result;
    }

    public function delete($recId)
    {
        $query = "DELETE FROM [InvoiceTemporary] WHERE [RecID] = ?";

        $statement = $this->connect()->prepare($query);
        $result = $statement->execute([$recId]);

        return $result;
    }

    public function calculateTotalsAndUpdate($recId)
    {
        $invoiceDetailTemp = new InvoiceDetailTemp();

        // Calculate the new totals
        $invoiceDetailRecords = $invoiceDetailTemp->findAllByInvoiceRecID($recId); // Retrieve related InvoiceDetailTMP records
        $totalSubTotal = 0;
        $totalVATAmount = 0;
        $grandTotal = 0;
        $balanceAmount = 0;

        foreach ($invoiceDetailRecords as $record) {
            $totalSubTotal += $record['SubTotal'];
            $totalVATAmount = $record['SalesTaxAmount'];
            $grandTotal += $record['TotalAmount'];
        }

        $results = $this->updateTotals($recId, $totalSubTotal, $totalVATAmount, $grandTotal, $balanceAmount);

        return $results;
    }

    public function updateTotalsIncremental($recId, $totalAmountChange)
    {
        // Check if the record exists
        $existingRecord = $this->findById($recId);
        if ($existingRecord === false) {
            return false; // Record not found
        }

        // Incrementally update the totals
        $totalSubTotal = $existingRecord['TotalSubTotal'] + $totalAmountChange;
        // Calculate VAT amount based on your logic
        $VATAmount = $totalAmountChange * 0.15; // Assuming 15% VAT
        $totalVATAmount = $existingRecord['TotalVATAmount'] + $VATAmount;
        $grandTotal = $existingRecord['GrandTotal'] + $totalAmountChange + $VATAmount;
        $balanceAmount = $existingRecord['BalanceAmount'] + $grandTotal;

        $results = $this->updateTotals($recId, $totalSubTotal, $totalVATAmount, $grandTotal, $balanceAmount);

        return $results;
    }

    public function InsertInvoiceTempToInvoice($recId)
    {
        $invoiceTemp = $this->findById($recId);
        $invoiceNumber = $invoiceTemp['InvoiceNumber'];
        $invoice = (new Invoice())->findByInvoiceNumber($invoiceNumber);
        $invoiceNumber = $invoiceNumber ? $invoice['InvoiceNumber'] : null;
        if ($invoiceNumber) {
            return $this->UpdateInvoiceWithInvoiceTempByInvoiceTemp($invoice, $invoiceTemp);
        }

        $invoiceNumber = (new Invoice())->generateInvoiceNumber();

        $currentDate = date("Y-m-d");

        $queryInvoice = "INSERT INTO [Invoice] (
            [CustomerCode],
            [CustomerRecID],
            [PriceTypeRecID],
            [InvoiceNumber],
            [InvoiceDate],
            [PaymentTermRecID],
            [PaymentMethodRecID],
            [CashierPerson],
            [SalesPerson],
            [TotalUnitAmount],
            [TotalDiscountAmount],
            [TotalSubTotal],
            [TotalVATAmount],
            [GrandTotal],
            [BalanceAmount],
            [CardAmount],
            [CashAmount],
            [Remarks],
            [StatusRecID],
            [CreatedBranchRecID],
            [CreatedBy],
            [CreatedDate],
            [CreatedTime],
            [ModifiedBy],
            [ModifiedDate],
            [ModifiedTime],
            [CollectionBy],
            [CollectionDate],
            [CollectionTime]
        )
        OUTPUT Inserted.RecID
        SELECT
            [CustomerCode],
            [CustomerRecID],
            [PriceTypeRecID],
            '" . $invoiceNumber . "',
            '" . $currentDate . "',
            [PaymentTermRecID],
            [PaymentMethodRecID],
            [CashierPerson],
            [SalesPerson],
            [TotalUnitAmount],
            [TotalDiscountAmount],
            [TotalSubTotal],
            [TotalVATAmount],
            [GrandTotal],
            [BalanceAmount],
            [CardAmount],
            [CashAmount],
            [Remarks],
            [StatusRecID],
            [CreatedBranchRecID],
            [CreatedBy],
            [CreatedDate],
            [CreatedTime],
            [ModifiedBy],
            [ModifiedDate],
            [ModifiedTime],
            [CollectionBy],
            [CollectionDate],
            [CollectionTime]
        FROM [InvoiceTemporary]
        WHERE [InvoiceTemporary].[RecID] = '" . $recId . "'";

        $statement = $this->connect()->prepare($queryInvoice);
        $statement->execute();
        $resultSet = $statement->fetch();
        $newlyCreatedInvoiceRecID = $resultSet['RecID'];

        $invoiceDetail = (new InvoiceDetailTemp())->insertInvoiceDetailTempToInvoiceDetailByInvoiceRecId($recId, $newlyCreatedInvoiceRecID);

        return $newlyCreatedInvoiceRecID;
    }

    public function InsertInvoiceTempToInvoiceHold($recId)
    {
        $newInvoice = $this->InsertInvoiceTempToInvoice($recId);
        $invoice = (new Invoice())->makeInvoiceHold($newInvoice);
        return $invoice;
    }

    public function InsertInvoiceToInvoiceTemp($recId)
    {
        $queryInvoice = "INSERT INTO [InvoiceTemporary] (
            [CustomerCode],
            [CustomerRecID],
            [PriceTypeRecID],
            [InvoiceNumber],
            [InvoiceDate],
            [PaymentTermRecID],
            [PaymentMethodRecID],
            [CashierPerson],
            [SalesPerson],
            [TotalUnitAmount],
            [TotalDiscountAmount],
            [TotalSubTotal],
            [TotalVATAmount],
            [GrandTotal],
            [BalanceAmount],
            [CardAmount],
            [CashAmount],
            [Remarks],
            [StatusRecID],
            [CreatedBranchRecID],
            [CreatedBy],
            [CreatedDate],
            [CreatedTime],
            [ModifiedBy],
            [ModifiedDate],
            [ModifiedTime],
            [CollectionBy],
            [CollectionDate],
            [CollectionTime]
        )
        OUTPUT Inserted.RecID
        SELECT
            [CustomerCode],
            [CustomerRecID],
            [PriceTypeRecID],
            [InvoiceNumber],
            [InvoiceDate],
            [PaymentTermRecID],
            [PaymentMethodRecID],
            [CashierPerson],
            [SalesPerson],
            [TotalUnitAmount],
            [TotalDiscountAmount],
            [TotalSubTotal],
            [TotalVATAmount],
            [GrandTotal],
            [BalanceAmount],
            [CardAmount],
            [CashAmount],
            [Remarks],
            [StatusRecID],
            [CreatedBranchRecID],
            [CreatedBy],
            [CreatedDate],
            [CreatedTime],
            [ModifiedBy],
            [ModifiedDate],
            [ModifiedTime],
            [CollectionBy],
            [CollectionDate],
            [CollectionTime]
        FROM [Invoice]
        WHERE [Invoice].[RecID] = '" . $recId . "'";

        $statement = $this->connect()->prepare($queryInvoice);
        $statement->execute();
        $resultSet = $statement->fetch();
        $newlyCreatedInvoiceRecID = $resultSet['RecID'];

        $invoiceDetailTemp = (new InvoiceDetailTemp())->insertInvoiceDetailToInvoiceDetailTempByInvoiceTempRecId($recId, $newlyCreatedInvoiceRecID);

        return $resultSet;
    }

    public function UpdateInvoiceWithInvoiceTempByInvoiceTemp($invoice, $invoiceTemp)
    {
        $currentDate = date("Y-m-d");
        $invoiceNumber = $invoiceTemp['InvoiceNumber'];

        // First, update the existing invoice record
        $updateInvoiceQuery = "UPDATE [Invoice]
                            SET [CustomerCode] = it.[CustomerCode],
                                [CustomerRecID] = it.[CustomerRecID],
                                [PriceTypeRecID] = it.[PriceTypeRecID],
                                [InvoiceDate] = it.[InvoiceDate],
                                [PaymentTermRecID] = it.[PaymentTermRecID],
                                [PaymentMethodRecID] = it.[PaymentMethodRecID],
                                [CashierPerson] = it.[CashierPerson],
                                [SalesPerson] = it.[SalesPerson],
                                [TotalUnitAmount] = it.[TotalUnitAmount],
                                [TotalDiscountAmount] = it.[TotalDiscountAmount],
                                [TotalSubTotal] = it.[TotalSubTotal],
                                [TotalVATAmount] = it.[TotalVATAmount],
                                [GrandTotal] = it.[GrandTotal],
                                [BalanceAmount] = it.[BalanceAmount],
                                [CardAmount] = it.[CardAmount],
                                [CashAmount] = it.[CashAmount],
                                [Remarks] = it.[Remarks],
                                [StatusRecID] = it.[StatusRecID],
                                [CreatedBranchRecID] = it.[CreatedBranchRecID],
                                [CreatedBy] = it.[CreatedBy],
                                [CreatedDate] = it.[CreatedDate],
                                [CreatedTime] = it.[CreatedTime],
                                [ModifiedBy] = it.[ModifiedBy],
                                [ModifiedDate] = it.[ModifiedDate],
                                [CollectionBy] = it.[CollectionBy],
                                [CollectionDate] = it.[CollectionDate],
                                [CollectionTime] = it.[CollectionTime]
                            FROM [InvoiceTemporary] AS it
                            WHERE [Invoice].[InvoiceNumber] = it.[InvoiceNumber]";

        $updateInvoiceStatement = $this->connect()->prepare($updateInvoiceQuery);
        $updateInvoiceStatement->execute([$invoiceNumber, $invoiceNumber]);

        $invoiceRecID = (new Invoice())->findByInvoiceNumber($invoiceNumber);

        (new InvoiceDetail())->deleteAllByInvoiceNumber($invoiceNumber);

        (new InvoiceDetailTemp())->insertInvoiceDetailTempToInvoiceDetailByInvoiceRecId($invoiceTemp['RecID'], $invoice['RecID']);

        return $invoiceRecID['RecID'];
    }

    public function addPayment($recId, $cashAmount, $cardAmount)
    {
        $condition = '';
        $total_amount_given = 0;
        // Set PaymentMethodRecID based on payment method
        if ($cashAmount) {
            $paymentMethodRecID = 1;
            $total_amount_given += $cashAmount;
            $condition = $condition . ", [CashAmount] = $cashAmount";
        } 
        if ($cardAmount) {
            $paymentMethodRecID = 2;
            $total_amount_given += $cardAmount;
            $condition = $condition . ", [CardAmount] = $cardAmount";
        }
        if ($cashAmount && $cardAmount) {
            $paymentMethodRecID = 5;
        }

        $queryGetGrandTotal = "SELECT [GrandTotal] FROM [InvoiceTemporary] WHERE [RecID] = ?";
        $statementGetGrandTotal = $this->connect()->prepare($queryGetGrandTotal);
        $statementGetGrandTotal->execute([$recId]);
        $grandTotalResult = $statementGetGrandTotal->fetch()['GrandTotal'] ?? null;
        $grandTotal = $grandTotalResult ? $grandTotalResult : 0;
        $balanceAmount = $total_amount_given - $grandTotal;

        $query = "UPDATE [InvoiceTemporary]
            SET [PaymentMethodRecID] = ?, [BalanceAmount] = ? $condition
          WHERE [RecID] = ?";

        $statement = $this->connect()->prepare($query);
        $statement->execute([$paymentMethodRecID, $balanceAmount, $recId]);
        return true;
    }
}
