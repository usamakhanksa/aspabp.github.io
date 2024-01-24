<?php

function canonicalizeXML($xmlString)
{
    $doc = new DOMDocument();
    $doc->loadXML($xmlString);

    if ($doc->documentElement) {
        $doc->documentElement->normalize();
        $canonicalized = $doc->C14N(true, false, null, null);
        return $canonicalized;
    }

    return null;
}

/**
 * Canonicalize an XML string and find the QR code value
 *
 * @param string $xmlString XML content
 * @return string|false QR code value or false if not found
 */
function findQRFromXML($xmlString)
{
    // Load XML content into a SimpleXMLElement object
    $xml = simplexml_load_string($xmlString);

    // Register XML namespaces
    $xml->registerXPathNamespace('cbc', 'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2');
    $xml->registerXPathNamespace('cac', 'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2');

    // Find the QR code value
    $result = $xml->xpath('//cac:AdditionalDocumentReference[cbc:ID="QR"]/cac:Attachment/cbc:EmbeddedDocumentBinaryObject');

    if (!empty($result)) {
        $qrCodeValue = (string)$result[0];
        return $qrCodeValue;
    } else {
        return false;
    }
}
