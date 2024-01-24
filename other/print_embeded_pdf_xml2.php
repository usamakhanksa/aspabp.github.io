<?php

use chillerlan\QRCode\QRCode;

$ROOT = $_SERVER["DOCUMENT_ROOT"];
require_once $ROOT . '/fatoora/vendor/autoload.php';
require_once $ROOT . '/fatoora/app/fatoora/FatooraInvoice.php';
require_once $ROOT . '/fatoora/app/fatoora/FatooraBusinessInvoice.php';
require_once $ROOT . '/fatoora/app/fatoora/FatooraSdk.php';

$invoiceNumber = $_GET['invoiceNumber'];
$invoiceType = $_GET['invoiceType'] ?? 'simplified';
$fatooraInvoice = $invoiceType == 'standard' ? new FatooraBusinessInvoice() : new FatooraInvoice($invoiceNumber);
$result = $fatooraInvoice->findInvoice($invoiceNumber);

if ($result != false) {
    // Load your XML content
    $xmlContent = base64_decode($result['Invoice']);
} else {
    die('No such invoice');
}

$html = file_get_contents('b2b_invoice_pdf.html');

$qrcode = (new QRCode())->render($result['QR'], 'qrcode.png');

$mpdf = new \Mpdf\Mpdf([
    'mode' => 'utf-8',
    'format' => 'A4',
    'PDFA' => true,
	'PDFAauto' => true,
]);

$mpdf->autoScriptToLang = true;
$mpdf->baseScript = 1;
$mpdf->autoArabic = true;
$mpdf->autoLangToFont = true;
$mpdf->nonPrintMargin = 0;
$mpdf->autoMarginPadding = 0;

$mpdf->SetDisplayMode('fullpage');

$mpdf->SetAssociatedFiles([[
	'name' => 'generated-standard-xml-invoice_signed.xml',
	'mime' => 'text/xml',
	'description' => 'Standard XML invoice',
	'AFRelationship' => 'Alternative',
	'path' => '../fatoora/xml-files/generated-simplified-xml-invoice_signed.xml'
]]);

$mpdf->WriteHTML($html);
$mpdf->Output();