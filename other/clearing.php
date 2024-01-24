<?php

$ROOT = $_SERVER["DOCUMENT_ROOT"];
require_once $ROOT . '/fatoora/vendor/autoload.php';
require_once $ROOT . '/fatoora/fatoora/csr-config.php';
require_once $ROOT . "/fatoora/app/invoice/Invoice.php";
require_once $ROOT . "/fatoora/app/invoiceDetail/InvoiceDetail.php";
require_once $ROOT . '/fatoora/app/fatoora/FatooraSettings.php';
require_once $ROOT . "/fatoora/app/customer/Customer.php";
require_once $ROOT . "/fatoora/app/fatoora/FatooraBusinessInvoice.php";
require_once $ROOT . "/fatoora/fatoora/utils.php";


use Bl\FatooraZatca;
use Bl\FatooraZatca\Objects\Client;
use Bl\FatooraZatca\Objects\Invoice as InvoiceXML;
use Bl\FatooraZatca\Objects\Seller;
use Bl\FatooraZatca\Objects\Setting;
use Bl\FatooraZatca\Objects\InvoiceItem;
use Bl\FatooraZatca\Services\ReportInvoiceService;
use Bl\FatooraZatca\Zatca;

$zatca = new Zatca();

$settings = (new FatooraSettings())->findSettings();
$seller = new Seller(
    $supplierVAT, $supplierStreetName, $supplierBuildingNumber, $supplierBuildingNumber, $supplierCityName, $supplierCityName, 
    $supplierPostalCode, $supplierVAT, $supplierName, $settings['private_key'], $settings['cert_production'], $settings['secret_production']);

$invoiceNumber = $_GET['invoiceNumber'];
$invoice = (new Invoice())->findBusinessInvoiceHeaderFooterByInvoiceNumber($invoiceNumber);

$invoiceRecID = $invoice['RecID'];
$invoiceNumber = $invoice['InvoiceNumber'];
$date =  $invoice['DATE'];
$deliveryDate =  $invoice['DeliveryDate'];
$time =  $invoice['TIME'];
$cash =  $invoice['CashAmount'];
$card =  $invoice['CardAmount'];
$balance =  $invoice['BalanceAmount'];
$subTotal =  $invoice['TotalSubTotal'];
$totalVAT =  $invoice['TotalVATAmount'];
$grandTotal =  $invoice['GrandTotal'];
$customerCode = $invoice['CustomerCode'];
$customerName =  $invoice['CustomerName'];
$customerNameAR =  $invoice['CustomerNameAR'];
$customerVAT =  $invoice['VATNumber'];
$remarks =  $invoice['Remarks'];

$fatooraInvoice = new FatooraBusinessInvoice();
$firstRecord = $fatooraInvoice->findFirstRecord();
$fatooraInvoiceDocument = $fatooraInvoice->findOrCreateInvoice($invoiceNumber);
$invoiceCounter = extractCounter($invoiceNumber);

if ($firstRecord == false) {
    $PIH = 'NWZlY2ViNjZmZmM4NmYzOGQ5NTI3ODZjNmQ2OTZjNzljMmRiYzIzOWRkNGU5MWI0NjcyOWQ3M2EyN2ZiNTdlOQ==';
} else {
    $invoiceNumberPrevious = getInvoiceNumberFromCounter((int)$invoiceCounter - 1, 'business');
    $fatooraInvoicePrevious = $fatooraInvoice->findInvoice($invoiceNumberPrevious);
    $PIH = $fatooraInvoicePrevious['InvoiceHash'];
}


$fatooraInvoice->setPIH($invoiceNumber, $PIH);
$uuid = $fatooraInvoiceDocument['UUID'];
// $customerIdentificationTypeCode = 'CRN';
$customerIdentificationTypeCode = 'NAT';


$customerDetails = (new Customer())->findCustomerDetails($customerCode);
$customerRegistrationNumber = $customerVAT;
$customerStreetName = !empty($customerDetails['StreetName']) ? $customerDetails['StreetName'] : (!empty($customerDetails['AddressLine']) ? $customerDetails['AddressLine'] : null);
$customerBuildingNumber = !empty($customerDetails['BuildingNumber']) ? $customerDetails['BuildingNumber'] : null;
$customerCityName = !empty($customerDetails['CityName']) ? $customerDetails['CityName'] : null;
$customerCountryCode = !empty(str_replace(' ', '', $customerDetails['CountryCode'])) ? str_replace(' ', '', $customerDetails['CountryCode']) : null;
$customerPostalCode = !empty(str_replace(' ', '', $customerDetails['POBox'])) ? str_replace(' ', '', $customerDetails['POBox']) : null;

if (empty($customerDetails)) {
    die400('Customer Details Are Mandatory For Standard Invoice Type');
} else if (empty($customerRegistrationNumber)) {
    die400('Customer Registration Number is Mandatory field');
} else if (empty($customerStreetName)) {
    die400('Customer Street Name is Mandatory field');
} else if (empty($customerBuildingNumber)) {
    die400('Customer Building Number is Mandatory field');
} else if (empty($customerCityName)) {
    die400('Customer City Name is Mandatory field');
} else if (empty($customerCountryCode)) {
    die400('Customer Country Code is Mandatory field');
} else if (empty($customerPostalCode)) {
    die400('Customer Postal Code Is Mandatory field');
}

$client = new Client($customerName, $customerVAT, $customerPostalCode, $customerStreetName, $customerBuildingNumber, $customerBuildingNumber, $customerCityName, $customerCityName);

$invoiceDetailRecords = (new InvoiceDetail())->findAllByBusinessInvoiceRecID($invoiceRecID);

$invoiceItems = [];
foreach ($invoiceDetailRecords as $invoiceDetailRecord) {
    $invoiceItem = new InvoiceItem(
        $invoiceDetailRecord['RecordNumber'], $invoiceDetailRecord['ProductFullName'], $invoiceDetailRecord['OrderQuantity'], 
        $invoiceDetailRecord['UnitAmount'], 0, $invoiceDetailRecord['SalesTaxAmount'], 15, $invoiceDetailRecord['TotalAmount']);
    $invoiceItems[] = $invoiceItem;
}

$invoice = new InvoiceXML($invoiceRecID, $invoiceNumber, $uuid, $date, $time, 388, 10, $subTotal, 0, $totalVAT, $grandTotal, $invoiceItems, previous_hash:$PIH, delivery_date:$deliveryDate);
$response = (new ReportInvoiceService($seller, $invoice, $client))->clearance();

$fatooraInvoice = new FatooraBusinessInvoice();
$fatooraInvoice->setInvoiceHash($invoiceNumber, $response['invoiceHash']);
$fatooraInvoice->setInvoiceUUID($invoiceNumber, $invoice->invoice_uuid);
$fatooraInvoice->setInvoiceBase64Encoded($invoiceNumber, $response['clearedInvoice']);
$fatooraInvoice->setPIH($invoiceNumber, $PIH);

$filePath = '../fatoora/xml-files/generated-standard-xml-invoice-2.xml';
file_put_contents($filePath, base64_decode($response['clearedInvoice']));

var_dump($response);
// echo '\n\n';
// echo 'Reporting Done';
