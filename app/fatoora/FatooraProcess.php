<?php

class InvoiceProcessing
{
    protected $invoice;
    protected $invoiceRemoved;
    protected $canonicalizedInvoice;
    protected $invoiceHash;
    protected $encodedInvoiceHash;

    public function __construct($invoice)
    {
        $this->invoice = $invoice;
    }

    public function getInvoice()
    {
        return $this->invoice;
    }

    public function setInvoice($invoice)
    {
        $this->invoice = $invoice;
    }

    public function getInvoiceRemoved()
    {
        return $this->invoiceRemoved;
    }

    public function setInvoiceRemoved($invoiceRemoved)
    {
        $this->invoiceRemoved = $invoiceRemoved;
    }

    public function getCanonicalizedInvoice()
    {
        return $this->canonicalizedInvoice;
    }

    public function setCanonicalizedInvoice($canonicalizedInvoice)
    {
        $this->canonicalizedInvoice = $canonicalizedInvoice;
    }

    public function getInvoiceHash()
    {
        return $this->invoiceHash;
    }

    public function setInvoiceHash($invoiceHash)
    {
        $this->invoiceHash = $invoiceHash;
    }

    public function getEncodedInvoiceHash()
    {
        return $this->encodedInvoiceHash;
    }

    public function setEncodedInvoiceHash($encodedInvoiceHash)
    {
        $this->encodedInvoiceHash = $encodedInvoiceHash;
    }
}


class InvoiceHashing extends InvoiceProcessing
{
    public function generateInvoiceHash()
    {
        $xml = new DOMDocument();
        $xml->loadXML($this->invoice);

        // Remove the tags mentioned in the table below using the XPath
        $xPath = new DOMXPath($xml);
        $xPath->registerNamespace('ds', 'http://www.w3.org/2000/09/xmldsig#');
        $xPath->registerNamespace('cbc', 'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2');
        $xPath->registerNamespace('cac', 'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2');

        // List of XPath queries to remove specific tags
        $queries = [
            '//*[local-name()="Invoice"]//*[local-name()="UBLExtensions"]',
            '//*[local-name()="AdditionalDocumentReference"][cbc:ID[normalize-space(text()) = "QR"]]',
            '//*[local-name()="Invoice"]//*[local-name()="Signature"]',
        ];

        foreach ($queries as $query) {
            $invoiceBody = $xPath->query($query);
            foreach ($invoiceBody as $node) {
                $node->parentNode->removeChild($node);
            }
        }

        // Remove the XML version
        $xml->removeChild($xml->firstChild);

        // $xml->documentElement->setAttribute('xmlns', 'http://www.w3.org/2000/09/xmldsig#');

        // Canonicalize the Invoice using the C14N11 standard
        $this->canonicalizedInvoice = $xml->C14N();

        // Hash the new invoice body using SHA-256 (output)
        $this->invoiceHash = hash('sha256', $this->canonicalizedInvoice);

        // Encode the hashed invoice using base64 (output)
        $this->encodedInvoiceHash = base64_encode($this->invoiceHash);

        // Using HEX-to Base64 Encoder e.g.:oRtv5YelD32v/jp/tC3MzzK0PumzfZ8lLQQkPlTBGj8=
        return $this->encodedInvoiceHash;
    }

    public function generateDigitalSignature()
    {
        /**
         * Generate private key from CSR config file (you can refer to openssl commands, or readme file on SDK)
         * Sign the generated invoice hash (in SHA-256 format not encoded with base64) with ECDSA using the private key (output). 
         * e.g.:MEQCIGvLa1f3uMCe0AidKUWJ5ghMiDMRcC0qO78ntcTKVOYgAiAKBkX+uuFhbIcye3JznNa45qH1twlLFu/qPzEQ9HMNLw==
         */
        $csrFile = '';
        $privateKey = file_get_contents($csrFile);
        $privateKey = openssl_pkey_get_private($privateKey);
    }

    public function generateCertificateHash($x509Certificate)
    {
        // Step 3: Generate Certificate Hash
        // Implementation to hash the certificate using SHA-256 and encode using base64
    }

    public function populateSignedPropertiesOutput($invoiceXML, $digestValue, $signingTime, $x509IssuerName, $x509SerialNumber)
    {
        // Step 4: Populate the Signed Properties Output
        // Implementation to populate signed properties in the XML invoice
    }

    public function generateSignedPropertiesHash()
    {
        // Step 5: Generate Signed Properties Hash
        // Implementation to hash the signed properties using SHA-256 and encode using base64
    }

    public function populateUBLExtensionsOutput()
    {
        // Step 6: 
        // Implementation to populate UBL extensions in the XML invoice
    }

    public function GenerateQRAndPopulateEncodedQR()
    {
        // Step 7: Generate QR and Populate Encoded QR
        // Implementation to generate a QR and populate the encoded QR in the XML invoice
    }
}

class XMLProcessor
{
    public function removeTagsFromInvoice($xmlFilePath, $xPathToRemove)
    {
        // Implementation to remove specific tags from the invoice XML using XPath
    }

    public function populateUBLExtensionsOutput($invoiceXML, $signatureValue, $x509Certificate, $digestValueInvoiceHash, $digestValueSignedProperties)
    {
        // Implementation to populate UBL Extensions in the XML invoice
    }

    // Other XML-related methods
}
