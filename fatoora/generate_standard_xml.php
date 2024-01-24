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

use Objects\Client\Client;
use Objects\Supplier\Supplier;
use Saleh7\Zatca\AdditionalDocumentReference;
use Saleh7\Zatca\Address;
use Saleh7\Zatca\AllowanceCharge;
use Saleh7\Zatca\BillingReference;
use Saleh7\Zatca\ClassifiedTaxCategory;
use Saleh7\Zatca\Contract;
use Saleh7\Zatca\Delivery;
use Saleh7\Zatca\ExtensionContent;
use Saleh7\Zatca\GeneratorInvoice;
use Saleh7\Zatca\InvoiceLine;
use Saleh7\Zatca\InvoiceType;
use Saleh7\Zatca\Item;
use Saleh7\Zatca\LegalEntity;
use Saleh7\Zatca\LegalMonetaryTotal;
use Saleh7\Zatca\Party;
use Saleh7\Zatca\PartyTaxScheme;
use Saleh7\Zatca\PaymentMeans;
use Saleh7\Zatca\Price;
use Saleh7\Zatca\Signature;
use \Saleh7\Zatca\SignatureInformation;
use Saleh7\Zatca\TaxCategory;
use Saleh7\Zatca\TaxScheme;
use Saleh7\Zatca\TaxSubTotal;
use Saleh7\Zatca\TaxTotal;
use Saleh7\Zatca\UBLDocumentSignatures;
use Saleh7\Zatca\UBLExtension;
use Saleh7\Zatca\UBLExtensions;
use Saleh7\Zatca\UnitCode;

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
$totalDiscount = $invoice['TotalDiscountAmount'] ?? 0.00;
$grandTotal =  $invoice['GrandTotal'];
$prepaidAmount = $invoice['CollectionAmount'];
$customerCode = $invoice['CustomerCode'];
$customerName =  $invoice['CustomerName'];
$customerNameAR =  $invoice['CustomerNameAR'];
$customerVAT =  $invoice['VATNumber'];
$remarks =  $invoice['Remarks'];


$fatooraInvoice = new FatooraBusinessInvoice();
$firstRecord = $fatooraInvoice->findFirstRecord();
$lastRecord = $fatooraInvoice->findLastRecord();
$invoiceCounter = extractCounter($invoiceNumber);
$lastInvoiceCounter = extractCounter($lastRecord['InvoiceNumber']);

if ($invoiceCounter - $lastInvoiceCounter !== 1) {
    $fatooraInvoice->deleteInvoice($invoiceNumber);
    die400("Error: The invoice number is not consecutive.");
}

$fatooraInvoiceDocument = $fatooraInvoice->findOrCreateInvoice($invoiceNumber);

if ($firstRecord == false) {
    $PIH = 'NWZlY2ViNjZmZmM4NmYzOGQ5NTI3ODZjNmQ2OTZjNzljMmRiYzIzOWRkNGU5MWI0NjcyOWQ3M2EyN2ZiNTdlOQ==';
} else {
    $invoiceNumberPrevious = getInvoiceNumberFromCounter((int)$invoiceCounter - 1, 'business');
    $fatooraInvoicePrevious = $fatooraInvoice->findInvoice($invoiceNumberPrevious);
    $PIH = $fatooraInvoicePrevious['InvoiceHash'];
}


$fatooraInvoice->setPIH($invoiceNumber, $PIH);
(new FatooraCommandExecutor())->setPIHInSdk($PIH);
$uuid = $fatooraInvoiceDocument['UUID'];
// $customerIdentificationTypeCode = 'CRN';
$customerIdentificationTypeCode = 'NAT';

$invoiceDetailRecords = (new InvoiceDetail())->findAllByBusinessInvoiceRecID($invoiceRecID);

// generating xml
// SignatureInformation
$sign = (new SignatureInformation)
    ->setReferencedSignatureID("urn:oasis:names:specification:ubl:signature:Invoice")
    ->setID('urn:oasis:names:specification:ubl:signature:1');

// UBLDocumentSignatures
$ublDecoment = (new UBLDocumentSignatures)
    ->setSignatureInformation($sign);

$extensionContent = (new ExtensionContent)
    ->setUBLDocumentSignatures($ublDecoment);

// UBLExtension
$UBLExtension[] = (new UBLExtension)
    ->setExtensionURI('urn:oasis:names:specification:ubl:dsig:enveloped:xades')
    ->setExtensionContent($extensionContent);

$UBLExtensions = (new UBLExtensions)
    ->setUBLExtensions($UBLExtension);

$signature = (new Signature)
    ->setId("urn:oasis:names:specification:ubl:signature:Invoice")
    ->setSignatureMethod("urn:oasis:names:specification:ubl:dsig:enveloped:xades");
// invoiceType object
$invoiceType = (new InvoiceType)
    ->setInvoice($invoiceTypeName) // Invoice / Simplified
    ->setInvoiceType('Invoice'); // Invoice / Debit / Credit
// invoiceType object
$inType = (new BillingReference)
    ->setId('Invoice');

// invoiceType object
$contact = (new Contract)
    ->setId('15');


$additionalDocumentReferences = [];

$additionalDocumentReferences[] = (new AdditionalDocumentReference)
    ->setId('ICV')
    ->setUUID($invoiceCounter);

$additionalDocumentReferences[] = (new AdditionalDocumentReference)
    ->setId('PIH')
    ->setPreviousInvoiceHash($PIH);

// $additionalDocumentReferences[] = (new \Saleh7\Zatca\AdditionalDocumentReference())
//     ->setId('QR');




$customerDetails = (new Customer())->findCustomerDetails($customerCode);
$customerRegistrationNumber = $customerVAT;
$customerStreetName = !empty($customerDetails['StreetName']) ? $customerDetails['StreetName'] : (!empty($customerDetails['AddressLine']) ? $customerDetails['AddressLine'] : null);
$customerBuildingNumber = !empty($customerDetails['BuildingNumber']) ? $customerDetails['BuildingNumber'] : null;
$customerCityName = !empty($customerDetails['CityName']) ? $customerDetails['CityName'] : null;
$customerCountryCode = !empty(str_replace(' ', '', $customerDetails['CountryCode'])) ? str_replace(' ', '', $customerDetails['CountryCode']) : null;
$customerPostalCode = !empty(str_replace(' ', '', $customerDetails['POBox'])) ? str_replace(' ', '', $customerDetails['POBox']) : null;

if (empty($customerDetails)) {
    $fatooraInvoice->deleteInvoice($invoiceNumber);
    die400('Customer Details Are Mandatory For Standard Invoice Type');
} else if (empty($customerRegistrationNumber)) {
    $fatooraInvoice->deleteInvoice($invoiceNumber);
    die400('Customer Registration Number is Mandatory field');
} else if (empty($customerStreetName)) {
    $fatooraInvoice->deleteInvoice($invoiceNumber);
    die400('Customer Street Name is Mandatory field');
} else if (empty($customerBuildingNumber)) {
    $fatooraInvoice->deleteInvoice($invoiceNumber);
    die400('Customer Building Number is Mandatory field');
} else if (empty($customerCityName)) {
    $fatooraInvoice->deleteInvoice($invoiceNumber);
    die400('Customer City Name is Mandatory field');
} else if (empty($customerCountryCode)) {
    $fatooraInvoice->deleteInvoice($invoiceNumber);
    die400('Customer Country Code is Mandatory field');
} else if (empty($customerPostalCode)) {
    $fatooraInvoice->deleteInvoice($invoiceNumber);
    die400('Customer Postal Code Is Mandatory field');
}

// Tax scheme
$taxScheme = (new TaxScheme)
    ->setId("VAT");


$supplier = new Supplier(
    $taxScheme,
    $supplierName,
    $supplierVAT,
    $supplierVAT,
    $supplierIdentificationTypeCode,
    $supplierStreetName,
    $supplierBuildingNumber,
    $supplierBuildingNumber,
    $supplierCityName,
    $supplierCityName,
    $supplierPostalCode,
    $supplierCountryCode
);

$client = new Client(
    $taxScheme,
    $customerName,
    $customerVAT,
    $customerRegistrationNumber,
    $customerIdentificationTypeCode,
    $customerStreetName,
    $customerBuildingNumber,
    $customerBuildingNumber,
    $customerCityName,
    $customerCityName,
    $customerPostalCode,
    $customerCountryCode
);

$delivery = (new Delivery)->setActualDeliveryDate($deliveryDate);
$clientPaymentMeans = (new PaymentMeans)->setPaymentMeansCode("10");
$taxCategory = (new TaxCategory)
    ->setPercent(15)
    ->setTaxScheme($taxScheme);


// Allowance charges
$allowanceCharges = [];
$allowanceCharges[] = (new AllowanceCharge)
    ->setChargeIndicator(false)
    ->setAllowanceChargeReason('discount')
    ->setAmount($totalDiscount)
    ->setTaxCategory($taxCategory);

$allowanceTotal = 0;
foreach ($allowanceCharges as $allowanceCharge){
    $allowanceTotal = $allowanceTotal + $allowanceCharge->getAmount();
}


$tolerance = 0.0001;
if (((float)$grandTotal - ((float)$subTotal + (float)$totalVAT)) > $tolerance) {
    $fatooraInvoice->deleteInvoice($invoiceNumber);
    die400('Main Invoice Total Calculation Are Wrong');
}


// tax total
$taxSubTotal = (new TaxSubTotal)
    ->setTaxableAmount($subTotal)
    ->setTaxAmount($totalVAT)
    ->setTaxCategory($taxCategory);

$taxTotal = (new TaxTotal)
    ->addTaxSubTotal($taxSubTotal)
    ->setTaxAmount($totalVAT);

$legalMonetaryTotal = (new LegalMonetaryTotal)
    ->setLineExtensionAmount($subTotal)
    ->setTaxExclusiveAmount($subTotal)
    ->setTaxInclusiveAmount($grandTotal)
    ->setPrepaidAmount($prepaidAmount)
    ->setPayableAmount($grandTotal)
    ->setAllowanceTotalAmount($allowanceTotal);

$classifiedTax = (new ClassifiedTaxCategory)
    ->setPercent(15)
    ->setTaxScheme($taxScheme);


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

    if ($subTotal != $unitAmount * $orderQuantity) {
        $fatooraInvoice->deleteInvoice($invoiceNumber);
        die400("Invoice Line for ProductRecID $productRecID - SubTotal Calculation Is Wrong");
    }

    // $x = abs((float)$totalAmount - ((float)$subTotal + (float)$salesTaxAmount));
    if (abs((float)$totalAmount - ((float)$subTotal + (float)$salesTaxAmount)) > $tolerance) {
        $fatooraInvoice->deleteInvoice($invoiceNumber);
        die400("Invoice Line for ProductRecID $productRecID - Total Calculation Are Wrong");
    }

    // Invoice Line tax totals
    $lineTaxTotal = (new TaxTotal())
        ->setTaxAmount($salesTaxAmount)
        ->setRoundingAmount($totalAmount);
        
    // Product
    $productItem = (new Item)
        ->setName($productFullName)
        ->setClassifiedTaxCategory($classifiedTax);

    // Price
    $price = (new Price)
        ->setUnitCode(UnitCode::UNIT)
        ->setPriceAmount($unitAmount);
    $invoiceLines[] = (new InvoiceLine)
        ->setUnitCode("PCE")
        ->setId($productRecID)
        ->setItem($productItem)
        ->setLineExtensionAmount($subTotal)
        ->setPrice($price)
        ->setTaxTotal($lineTaxTotal)
        ->setInvoicedQuantity($orderQuantity);
}


// Invoice object
$invoice = (new \Saleh7\Zatca\Invoice())
    // ->setUBLExtensions($UBLExtensions)
    ->setUUID($uuid)
    ->setId($invoiceNumber)
    ->setIssueDate(new \DateTime())
    ->setIssueTime(new \DateTime())
    ->setInvoiceType($invoiceType)
    // ->Signature($signature)
    ->setContract($contact)
    // ->setBillingReference($inType)
    ->setAdditionalDocumentReferences($additionalDocumentReferences)
    ->setDelivery($delivery)
    ->setAllowanceCharges($allowanceCharges)
    ->setPaymentMeans($clientPaymentMeans)
    ->setTaxTotal($taxTotal)
    ->setInvoiceLines($invoiceLines)
    ->setLegalMonetaryTotal($legalMonetaryTotal)
    ->setAccountingCustomerParty($client)
    ->setAccountingSupplierParty($supplier);



$generatorXml = new GeneratorInvoice();
$outputXML = $generatorXml->invoice($invoice);
$encodedInvoice = encodeXMLtoBase64($outputXML);
$fatooraInvoice->setInvoiceBase64Encoded($invoiceNumber, $encodedInvoice);
$fatooraInvoice->setCreationStatus($invoiceNumber, 2); // status created

$filePath = 'xml-files/generated-standard-xml-invoice.xml';
file_put_contents($filePath, $outputXML);
// echo 'xml file generated successfully';
