<?php

$ROOT = $_SERVER["DOCUMENT_ROOT"];
require_once $ROOT . '/fatoora/vendor/autoload.php';
require_once $ROOT . '/fatoora/app/fatoora/FatooraApi.php';
require_once $ROOT . '/fatoora/app/fatoora/FatooraSdk.php';
require_once $ROOT . '/fatoora/app/fatoora/FatooraInvoice.php';

try {
        // $invoiceNumber = $_GET['invoiceNumber']; // first invoiceRecID

        // // generate the csr properties file
        // include_once $ROOT . '/fatoora/fatoora/generate_xml.php';

        // // generate invoice hash
        // $fatooraCommand = new FatooraCommandExecutor();
        // $output = $fatooraCommand->generateInvoiceHash($fatooraCommand->xmlFilePath . '/generated-xml-invoice.xml');
        // $hash = $fatooraCommand->extractInvoiceHash($output);
        // // $fatooraCommand->printArrayLineByLine($output);

        // // save hash to database
        // $fatooraInvoice = new FatooraInvoice();
        // $response = $fatooraInvoice->setInvoiceHash($invoiceNumber, $hash);

        // run the compliance invoice api
        $fatooraApi = new ComplianceInvoiceAPI();
        $response = $fatooraApi->postRequest();
        echo $response;
} catch (Exception $e) {
    echo $e;
}
