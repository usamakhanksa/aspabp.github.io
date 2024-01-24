<?php

$ROOT = $_SERVER["DOCUMENT_ROOT"];
require_once $ROOT . '/fatoora/vendor/autoload.php';
require_once $ROOT . "/fatoora/app/fatoora/FatooraApi.php";
require_once $ROOT . "/fatoora/app/fatoora/CSIDTemp.php";
require_once $ROOT . "/fatoora/app/fatoora/FatooraSdk.php";

// $fatooraApi = new ComplianceCSIDAPI();
// $fatooraApi->setOTP('123345');
// $response = $fatooraApi->postRequest();
// echo $response;
// echo '\n';
// echo $csidtemp = (new CSIDTemp())->processAndSaveResponse($response);

// $fatooraApi = new ComplianceInvoiceAPI();
// $response = $fatooraApi->postRequest();

// $fatooraApi = new ProductionCSIDOnboardingAPI();
// $response = $fatooraApi->postRequest();

// $fatooraApi = new ProductionCSIDRenewalAPI();
// $fatooraApi->setOTP('123456');
// $response = $fatooraApi->postRequest();

// $invoiceNumber = 'INV/00000155862';
// $fatooraApi = new ReportingAPI($invoiceNumber);
// $response = $fatooraApi->postRequest();

// $invoiceNumber = 'INV/00000155862';
// $fatooraApi = new ClearanceAPI($invoiceNumber);
// $response = $fatooraApi->postRequest();

$fatooraCommand = new FatooraCommandExecutor();
$output = $fatooraCommand->generateCSR();
$fatooraCommand->printArrayLineByLine($output);
echo 'generated successfully';

// echo $response;

// header('Content-type: application/json');
// echo json_encode($response);

