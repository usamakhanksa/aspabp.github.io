<?php

$ROOT = $_SERVER["DOCUMENT_ROOT"];
require_once $ROOT . '/fatoora/vendor/autoload.php';
require_once $ROOT . '/fatoora/app/fatoora/FatooraBusinessInvoice.php';
require_once $ROOT . '/fatoora/app/fatoora/FatooraApi.php';
require_once $ROOT . '/fatoora/app/fatoora/utils/Utils.php';

$fatooraInvoice = new FatooraBusinessInvoice();
$notReportedArray = $fatooraInvoice->findAllInvoiceNotReported();

foreach($notReportedArray as $notReportedInvoice) {
    $invoiceNumber = $notReportedInvoice['InvoiceNumber'];
    // run the reporting invoice api
    $fatooraApi = new ClearanceAPI($invoiceNumber);
    $response = $fatooraApi->postRequest();

    // check if the reporting was a sucess else break
    $result = $fatooraInvoice->findInvoiceStatus($invoiceNumber);
    if ($result['ReportingStatusRecID'] != 1) {
        break;
    } 
}

$processStopedInvoice = $fatooraInvoice->findBulkReportingStopedInvoice();

if (!$processStopedInvoice) {
    echo "No invoices Cleared";
} else {
    $invoiceNumber = $processStopedInvoice['InvoiceNumber'];
    echo "Invoice Clearing stoped at invoice: $invoiceNumber";
}
