<?php

function extractCounter($invoiceNumber)
{
    // Split the string by '/'
    $parts = explode('/', $invoiceNumber);

    // Get the last part of the split (the counter)
    $counter = end($parts);

    // Remove any characters after the last '/'
    $counter = preg_replace('/[^0-9]/', '', $counter);

    // Remove leading zeros
    $counter = ltrim($counter, '0');

    return $counter;
}

function getInvoiceNumberFromCounter(int $counter, $type = 'pos')
{
    $newInvoiceNumberInt = $counter;
    $prefix = $type == 'pos' ? 'INV/' . '00000' : 'INV/W' . '00';
    $formattedInvoiceNumber = $prefix . $newInvoiceNumberInt;
    return $formattedInvoiceNumber;
}

function generateUUID()
{
    return sprintf(
        '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0x0fff) | 0x4000,
        mt_rand(0, 0x3fff) | 0x8000,
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff)
    );
}

function encodeXMLtoBase64($xmlData)
{
    // Load the XML data
    $xml = new DOMDocument();
    $xml->loadXML($xmlData);

    // Convert XML to string
    $xmlString = $xml->saveXML();

    // Encode XML string to base64
    $base64Encoded = base64_encode($xmlString);

    return $base64Encoded;
}


function die400($e) {
    http_response_code(400);
    echo $e;
    die();
}

function die400AndDeleteCurrentInvoice($e) {
    http_response_code(400);
    echo $e;
    die();
}

