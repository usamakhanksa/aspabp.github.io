<?php

$ROOT = $_SERVER["DOCUMENT_ROOT"];
require_once $ROOT . '/fatoora/vendor/autoload.php';
require_once $ROOT . '/fatoora/app/fatoora/FatooraSdk.php';
require_once $ROOT . '/fatoora/app/fatoora/FatooraBusinessInvoice.php';

try {
    if (isset($_GET['invoiceNumber'])) {
        $invoiceNumber = $_GET['invoiceNumber'];

        // generate the csr properties file
        include_once $ROOT . '/fatoora/fatoora/generate_standard_xml.php';

        // sign the invoice
        // generate invoice hash
        $fatooraCommand = new FatooraCommandExecutor();
        $output = $fatooraCommand->signAndGenerateInvoiceHash($fatooraCommand->xmlFilePath . '/generated-standard-xml-invoice.xml', 'business');
        // $fatooraCommand->printArrayLineByLine($output);

        // create api request json file
        $output = $fatooraCommand->generateJsonApiRequest($fatooraCommand->xmlFilePath . '/generated-standard-xml-invoice_signed.xml', $fatooraCommand->fileRootPath . '/api-request.json');
        $fatooraCommand->saveInvoiceDetaisFromApiRequestJson($invoiceNumber, 'business');
        // $fatooraCommand->printArrayLineByLine($output);

        echo 'Successs';
    }
} catch (Exception $e) {
    die($e);
}
