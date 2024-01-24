<?php

$ROOT = $_SERVER["DOCUMENT_ROOT"];
require_once $ROOT . '/fatoora/vendor/autoload.php';
require_once $ROOT . '/fatoora/app/fatoora/FatooraInvoice.php';
require_once $ROOT . '/fatoora/app/fatoora/utils/Utils.php';

use BaconQrCode\Encoder\Encoder;
use BaconQrCode\Renderer\Image\ImagickImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

try {
    if (isset($_GET['invoiceNumber'])) {
        $invoiceNumber = $_GET['invoiceNumber'];

        $fatooraInvoice = new FatooraInvoice();
        $result = $fatooraInvoice->findInvoice($invoiceNumber);

        $qr = $result['QR'];
        $qr_code = base64_decode($qr);

        // Create QR code image
        $renderer = new ImageRenderer(new RendererStyle(400), new ImagickImageBackEnd());

        $writer = new Writer($renderer);
        $imageData = $writer->writeString($qr_code);

        // Output the image as a file (change the file path as needed)
        $imagePath = 'qr_code.png'; // Change this path to your desired file path
        file_put_contents($imagePath, $imageData);

        echo 'QR code image generated and saved at: ' . $imagePath;
    }
} catch (Exception $e) {
    die($e);
}
