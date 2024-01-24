<?php

$ROOT = $_SERVER["DOCUMENT_ROOT"];
require_once $ROOT . '/fatoora/vendor/autoload.php';
require_once $ROOT . '/fatoora/app/fatoora/FatooraInvoice.php';
require_once $ROOT . '/fatoora/app/fatoora/FatooraApi.php';
require_once $ROOT . '/fatoora/app/fatoora/utils/Utils.php';
require_once $ROOT . '/fatoora/fatoora/utils.php';

// set the header to application json
header('Content-Type: application/json');

$fatooraInvoice = new FatooraInvoice();
$notReportedArray = $fatooraInvoice->findAllInvoiceNotReported();

foreach($notReportedArray as $notReportedInvoice) {
    $invoiceNumber = $notReportedInvoice['InvoiceNumber'];
    // run the reporting invoice api
    $fatooraApi = new ReportingAPI($invoiceNumber);
    $response = $fatooraApi->postRequest();

    // check if the reporting was a sucess else break
    $result = $fatooraInvoice->findInvoiceStatus($invoiceNumber);
    if ($result['ReportingStatusRecID'] != 1) {
        break;
    } else {
        echo json_encode(["status" => "success", "message" => "Reported Invoice: $invoiceNumber"]);
        flush(); // Send the output to the browser
        ob_flush(); // Flush the output buffer
        usleep(5000000);
    }
}

$processStopedInvoice = $fatooraInvoice->findBulkReportingStopedInvoice();

if (!$processStopedInvoice) {
    echo json_encode(["status" => "success", "message" => "No Invoices Reported"]);
} else {
    $invoiceNumber = $processStopedInvoice['InvoiceNumber'];
    echo json_encode(["status" => "success", "message" => "Invoice Reporting stoped at invoice: $invoiceNumber"]);
}
