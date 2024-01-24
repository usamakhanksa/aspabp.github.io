<?php

$ROOT = $_SERVER["DOCUMENT_ROOT"];
require_once $ROOT . '/fatoora/vendor/autoload.php';
require_once $ROOT . "/fatoora/login/utils.php";
require_once $ROOT . "/fatoora/app/invoice/Invoice.php";
require_once $ROOT . "/fatoora/app/customer/Customer.php";
require_once $ROOT . "/fatoora/app/invoiceDetail/InvoiceDetail.php";
require_once $ROOT . "/fatoora/app/fatoora/FatooraBusinessInvoice.php";
require_once $ROOT . "/fatoora/app/fatoora/FatooraSdk.php";
require_once $ROOT . "/fatoora/app/fatoora/objects/Client.php";
require_once $ROOT . "/fatoora/app/fatoora/objects/Supplier.php";
require_once $ROOT . "/fatoora/fatoora/utils.php";
require_once $ROOT . "/fatoora/fatoora/csr-config.php";

session_start();

$invoiceNumber = $_GET['invoiceNumber'];
$invoiceTypeName = $_GET['invoiceType'] ?? 'Invoice'; // Invoice / Simplified
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
$fatooraInvoiceDocument = $fatooraInvoice->findInvoice($invoiceNumber);

$PIH = $fatooraInvoiceDocument['PIH'];
$uuid = $fatooraInvoiceDocument['UUID'];

$customerDetails = (new Customer())->findCustomerDetails($customerCode);
$customerRegistrationNumber = $customerVAT;
$customerStreetName = !empty($customerDetails['StreetName']) ? $customerDetails['StreetName'] : (!empty($customerDetails['AddressLine']) ? $customerDetails['AddressLine'] : null);
$customerBuildingNumber = !empty($customerDetails['BuildingNumber']) ? $customerDetails['BuildingNumber'] : null;
$customerCityName = !empty($customerDetails['CityName']) ? $customerDetails['CityName'] : null;
$customerCountryCode = !empty(str_replace(' ', '', $customerDetails['CountryCode'])) ? str_replace(' ', '', $customerDetails['CountryCode']) : null;
$customerPostalCode = !empty(str_replace(' ', '', $customerDetails['POBox'])) ? str_replace(' ', '', $customerDetails['POBox']) : null;

?>

<?php 

$invoiceDetailRecords = (new InvoiceDetail())->findAllByBusinessInvoiceRecID($invoiceRecID);

// Invoice Line(s)
$invoiceLines = [];
foreach ($invoiceDetailRecords as $invoiceDetailRecord) {
    $salesTaxAmount = $invoiceDetailRecord['SalesTaxAmount'];
    $unitAmount = $invoiceDetailRecord['UnitAmount'];
    $totalAmount = $invoiceDetailRecord['TotalAmount'];
    $productFullName = $invoiceDetailRecord['ProductFullName'];
    $productRecID = $invoiceDetailRecord['ProductRecID'];
    $subTotal = $invoiceDetailRecord['SubTotal'];
    $orderQuantity = $invoiceDetailRecord['OrderQuantity'];
}

?>

