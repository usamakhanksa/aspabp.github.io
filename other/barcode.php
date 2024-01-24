<?php

$ROOT = $_SERVER["DOCUMENT_ROOT"];
require_once $ROOT . '/fatoora/vendor/autoload.php';

session_start();

// Data to encode in the QR code (you can customize this)
$qrData = $_GET['text'];

use chillerlan\QRCode\QRCode;

try {
    header('Content-Type: image/png');
    echo '<img src="'.(new QRCode)->render($qrData).'" alt="QR Code" />';
} catch (\chillerlan\QRCode\QRCodeException $e) {
    echo 'Error: ' . $e->getMessage();
    exit;
}
