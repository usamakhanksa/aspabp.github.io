<?php

$ROOT = $_SERVER["DOCUMENT_ROOT"];
require_once $ROOT . '/fatoora/vendor/autoload.php';
require_once $ROOT . '/fatoora/app/fatoora/FatooraApi.php';
require_once $ROOT . '/fatoora/app/fatoora/FatooraSdk.php';
require_once $ROOT . '/fatoora/app/fatoora/utils/Utils.php';

try {
    if (isset($_GET['invoiceNumber']) || isset($_GET['invoiceType'])) {
        $invoiceNumber = $_GET['invoiceNumber'];

        // generate the csr properties file
        include_once $ROOT . '/fatoora/fatoora/generate_xml.php';

        // sign the invoice
        // generate invoice hash
        $fatooraCommand = new FatooraCommandExecutor();
        $output = $fatooraCommand->signAndGenerateInvoiceHash($fatooraCommand->xmlFilePath . '/generated-simplified-xml-invoice.xml');
        // $hash = $fatooraCommand->extractInvoiceHash($output);
        // $fatooraCommand->printArrayLineByLine($output);
        // echo '<br>';

        // create api request json file
        $output = $fatooraCommand->generateJsonApiRequest($fatooraCommand->xmlFilePath . '/generated-simplified-xml-invoice_signed.xml', $fatooraCommand->fileRootPath . '/api-request.json');
        $fatooraCommand->saveInvoiceDetaisFromApiRequestJson($invoiceNumber);
        // $fatooraCommand->printArrayLineByLine($output);
        // echo '<br>';

        // find and save the qrcode
        $contents = file_get_contents($fatooraCommand->xmlFilePath . '/generated-simplified-xml-invoice_signed.xml');
        $qr = findQRFromXML($contents); // base64 encoded QR code
        $fatooraInvoice->setQR($invoiceNumber, $qr);  

        // run the reporting invoice api
        $fatooraApi = new ReportingAPI($invoiceNumber);
        $response = $fatooraApi->postRequest();
        echo $response;
    }
} catch (Exception $e) {
    die($e);
}
