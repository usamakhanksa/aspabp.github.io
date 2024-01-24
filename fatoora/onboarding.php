<?php

$ROOT = $_SERVER["DOCUMENT_ROOT"];
require_once $ROOT . '/fatoora/vendor/autoload.php';
require_once $ROOT . '/fatoora/app/fatoora/FatooraApi.php';
require_once $ROOT . '/fatoora/app/fatoora/FatooraSdk.php';

try {
    if (isset($_GET['otp'])) {
        $otp = $_GET['otp']; // test otp is 123345

        // generate the csr properties file
        $fatooraCommand = new FatooraCommandExecutor();
        $fatooraCommand->generateCSR();
        $csrContents = $fatooraCommand->getFileContents($fatooraCommand->generatedCsr);

        // send csr to fatoora server and get back a signed certificate in pem format
        $fatooraApi = new ComplianceCSIDAPI();
        $fatooraApi->setCSR($csrContents);
        $fatooraApi->setOTP($otp);
        $response = $fatooraApi->postRequest();
        echo $response;
    }
} catch (Exception $e) {
    echo $e;
}
