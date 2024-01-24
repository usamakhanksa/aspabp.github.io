<?php

$ROOT = $_SERVER["DOCUMENT_ROOT"];
require_once $ROOT . '/fatoora/vendor/autoload.php';
require_once $ROOT . "/fatoora/login/utils.php";
require_once $ROOT . "/fatoora/app/invoice/Invoice.php";
require_once $ROOT . "/fatoora/app/customer/Customer.php";
require_once $ROOT . "/fatoora/app/invoiceDetail/InvoiceDetail.php";
require_once $ROOT . "/fatoora/app/fatoora/FatooraInvoice.php";
require_once $ROOT . "/fatoora/fatoora/utils.php";

session_start();

$supplierName = 'EMTYAZ FOR CATERING COMPANY';
$supplierVAT = '300066889400003';
$supplierIdentificationTypeCode = 'CRN';
$supplierStreetName = 'Al Azizia Behind Mass Transit';
$supplierBuildingNumber = '2929';
$supplierCityName = 'Riyadh';
$supplierCountryCode = 'SA';
$supplierPostalCode = '23344';

$invoiceRecID = $_GET['invoiceRecID'];
$invoice = (new Invoice())->findInvoiceHeaderFooter($invoiceRecID);

$invoiceNumber = $invoice['InvoiceNumber'];
$date =  $invoice['Date'];
$time =  $invoice['Time'];
$staff =  $invoice['CashierID'];
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

$fatooraInvoiceDocument = (new FatooraInvoice())->findOrCreateInvoice($invoiceNumber);


$PIH = $fatooraInvoiceDocument['PIH'];
$isPIH = empty($PIH) == true ? false : true;
// $PIH = 'nzPSlf+bp71zze+fD6g+yuTJs249l4ArEVVtwxSImT4=';
$invoiceCounter = extractCounter($invoiceNumber);
$uuid = $fatooraInvoiceDocument['UUID'];

// $customerIdentificationTypeCode = 'CRN';
$customerIdentificationTypeCode = 'NAT';

$invoiceRecID = $_GET['invoiceRecID'];
$invoiceDetailRecords = (new InvoiceDetail())->findAllByInvoiceRecID($invoiceRecID);

// generating xml
// SignatureInformation
$sign = (new \Saleh7\Zatca\SignatureInformation)
    ->setReferencedSignatureID("urn:oasis:names:specification:ubl:signature:Invoice")
    ->setID('urn:oasis:names:specification:ubl:signature:1');

// UBLDocumentSignatures
$ublDecoment = (new \Saleh7\Zatca\UBLDocumentSignatures)
    ->setSignatureInformation($sign);

$extensionContent = (new \Saleh7\Zatca\ExtensionContent)
    ->setUBLDocumentSignatures($ublDecoment);

// UBLExtension
$UBLExtension[] = (new \Saleh7\Zatca\UBLExtension)
    ->setExtensionURI('urn:oasis:names:specification:ubl:dsig:enveloped:xades')
    ->setExtensionContent($extensionContent);

$UBLExtensions = (new \Saleh7\Zatca\UBLExtensions)
    ->setUBLExtensions($UBLExtension);

$signature = (new \Saleh7\Zatca\Signature)
    ->setId("urn:oasis:names:specification:ubl:signature:Invoice")
    ->setSignatureMethod("urn:oasis:names:specification:ubl:dsig:enveloped:xades");
// invoiceType object
$invoiceType = (new \Saleh7\Zatca\InvoiceType())
    ->setInvoice('Invoice') // Invoice / Simplified
    ->setInvoiceType('Invoice'); // Invoice / Debit / Credit
// invoiceType object
$inType = (new \Saleh7\Zatca\BillingReference())
    ->setId('Invoice');

// invoiceType object
$contact = (new \Saleh7\Zatca\Contract())
    ->setId('15');


$additionalDocumentReferences = [];

$additionalDocumentReferences[] = (new \Saleh7\Zatca\AdditionalDocumentReference())
    ->setId('ICV')
    ->setUUID($invoiceCounter);

$additionalDocumentReferences[] = (new \Saleh7\Zatca\AdditionalDocumentReference())
    ->setId('PIH')
    ->setPreviousInvoiceHash($PIH);

// $additionalDocumentReferences[] = (new \Saleh7\Zatca\AdditionalDocumentReference())
//     ->setId('QR');




$customerDetails = (new Customer())->findCustomerDetails($customerCode);
$customerRegistrationNumber = $customerVAT;
$customerStreetName = !empty($customerDetails['StreetName']) ? $customerDetails['StreetName'] : (!empty($customerDetails['AddressLine']) ? $customerDetails['AddressLine'] : 'No Street Given');
$customerBuildingNumber = $customerDetails['BuildingNumber'] ?? '0000';
$customerCityName = $customerDetails['CityName'];
$customerCountryCode = str_replace(' ', '', $customerDetails['CountryCode']);
$customerPostalCode = str_replace(' ', '', $customerDetails['POBox']) ?? '00000';


// Tax scheme
$taxScheme = (new \Saleh7\Zatca\TaxScheme())
    ->setId("VAT");

    
// Supplier
$partyTaxSchemeSupplier = (new \Saleh7\Zatca\PartyTaxScheme())
    ->setTaxScheme($taxScheme)
    ->setCompanyId($supplierVAT);

$addressSupplier = (new \Saleh7\Zatca\Address())
    ->setStreetName($supplierStreetName)
    ->setBuildingNumber($supplierBuildingNumber)
    ->setPlotIdentification($supplierBuildingNumber)
    ->setCitySubdivisionName($supplierCityName)
    ->setCityName($supplierCityName)
    ->setPostalZone($supplierPostalCode)
    ->setCountry($supplierCountryCode);

$legalEntitySupplier = (new \Saleh7\Zatca\LegalEntity())
    ->setRegistrationName($supplierName);

$supplierCompany = (new \Saleh7\Zatca\Party())
    ->setPartyIdentification($supplierVAT)
    ->setPartyIdentificationId($supplierIdentificationTypeCode)
    ->setLegalEntity($legalEntitySupplier)
    ->setPartyTaxScheme($partyTaxSchemeSupplier)
    ->setPostalAddress($addressSupplier);


// Customer
$partyTaxSchemeCustomer = (new \Saleh7\Zatca\PartyTaxScheme())
    ->setTaxScheme($taxScheme);
    // ->setCompanyId($customerRegistrationNumber);

$addressCustomer = (new \Saleh7\Zatca\Address())
    ->setStreetName($customerStreetName)
    ->setBuildingNumber($customerBuildingNumber)
    ->setPlotIdentification($customerBuildingNumber)
    ->setCitySubdivisionName($customerCityName)
    ->setCityName($customerCityName)
    ->setPostalZone($customerPostalCode)
    ->setCountry($customerCountryCode);

$legalEntityCustomer = (new \Saleh7\Zatca\LegalEntity())
    ->setRegistrationName($customerName);

$customerCompany = (new \Saleh7\Zatca\Party())
    ->setPartyIdentification($customerRegistrationNumber)
    ->setPartyIdentificationId($customerIdentificationTypeCode)
    ->setLegalEntity($legalEntityCustomer)
    ->setPartyTaxScheme($partyTaxSchemeCustomer)
    ->setPostalAddress($addressCustomer);




$delivery = (new \Saleh7\Zatca\Delivery())
    ->setActualDeliveryDate("2022-09-07");

$clientPaymentMeans = (new \Saleh7\Zatca\PaymentMeans())
    ->setPaymentMeansCode("10");

$taxCategory = (new \Saleh7\Zatca\TaxCategory())
    ->setPercent(15)
    ->setTaxScheme($taxScheme);






// Allowance charges
$allowanceCharges = [];
$allowanceCharges[] = (new \Saleh7\Zatca\AllowanceCharge())
    ->setChargeIndicator(false)
    ->setAllowanceChargeReason('discount')
    ->setAmount(0.00)
    ->setTaxCategory($taxCategory);


// tax total
$taxSubTotal = (new \Saleh7\Zatca\TaxSubTotal())
    ->setTaxableAmount($subTotal)
    ->setTaxAmount($totalVAT)
    ->setTaxCategory($taxCategory);

$taxTotal = (new \Saleh7\Zatca\TaxTotal())
    ->addTaxSubTotal($taxSubTotal)
    ->setTaxAmount($totalVAT);

$legalMonetaryTotal = (new \Saleh7\Zatca\LegalMonetaryTotal())
    ->setLineExtensionAmount($subTotal)
    ->setTaxExclusiveAmount($subTotal)
    ->setTaxInclusiveAmount($grandTotal)
    ->setPrepaidAmount(0)
    ->setPayableAmount($grandTotal)
    ->setAllowanceTotalAmount(0);

$classifiedTax = (new \Saleh7\Zatca\ClassifiedTaxCategory())
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
    // Invoice Line tax totals
    $lineTaxTotal = (new \Saleh7\Zatca\TaxTotal())
        ->setTaxAmount($salesTaxAmount)
        ->setRoundingAmount($totalAmount);
    // Product
    $productItem = (new \Saleh7\Zatca\Item())
        ->setName($productFullName)
        ->setClassifiedTaxCategory($classifiedTax);
    // Price
    $price = (new \Saleh7\Zatca\Price())
        ->setUnitCode(\Saleh7\Zatca\UnitCode::UNIT)
        ->setPriceAmount($unitAmount);
    $invoiceLines[] = (new \Saleh7\Zatca\InvoiceLine())
        ->setUnitCode("PCE")
        ->setId($productRecID)
        ->setItem($productItem)
        ->setLineExtensionAmount($subTotal)
        ->setPrice($price)
        ->setTaxTotal($lineTaxTotal)
        ->setInvoicedQuantity($orderQuantity);

    // Invoice line net amount = (Invoiced quantity * (Item net price / item price base quantity)) + Sum of invoice line charge amount - Sum of invoice line allowance amount.
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
    ->setAccountingCustomerParty($customerCompany)
    ->setAccountingSupplierParty($supplierCompany);



$generatorXml = new \Saleh7\Zatca\GeneratorInvoice();
$outputXML = $generatorXml->invoice($invoice);
$encodedInvoice = encodeXMLtoBase64($outputXML);
$fatooraInvoiceDocument = (new FatooraInvoice())->setInvoiceBase64Encoded($invoiceNumber, $encodedInvoice);

$filePath = '../fatoora/xml-files/test-xml-invoice-2.xml';
file_put_contents($filePath, $outputXML);
echo 'done';

