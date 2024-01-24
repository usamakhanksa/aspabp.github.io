<?php

$ROOT = $_SERVER["DOCUMENT_ROOT"];
require_once $ROOT . "/fatoora/app/fatoora/CSIDTemp.php";
require_once $ROOT . "/fatoora/app/fatoora/CSIDProduction.php";
require_once $ROOT . "/fatoora/app/fatoora/CSIDRenewal.php";
require_once $ROOT . "/fatoora/app/fatoora/FatooraInvoice.php";
require_once $ROOT . "/fatoora/app/fatoora/FatooraBusinessInvoice.php";

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;


class FatooraApi
{
    protected $base_url = 'https://gw-fatoora.zatca.gov.sa/e-invoicing/developer-portal';
    protected $accept_language = 'en';
    protected $accept_version = 'V2';
    protected $body;
    protected $headers;
    protected $endpoint = '/';
    protected $response;
    protected $method;

    public function __construct()
    {
        $this->method = 'POST';
    }

    public function setBody()
    {
        $this->body = json_encode('');
    }

    public function setMethod($method)
    {
        $this->method = $method;
    }

    protected function setHeaders($additionalHeaders = [])
    {
        $baseHeaders = [
            'Accept-Language: ' . $this->accept_language,
            'Accept-Version: ' . $this->accept_version,
            'Content-Type: application/json'
        ];

        $this->headers = array_merge($baseHeaders, $additionalHeaders);
    }

    public function postRequest()
    {
        $this->setBody();
        $this->setHeaders();
        $url = $this->base_url . $this->endpoint;

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $this->method);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $this->body);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);

        $ROOT = $_SERVER["DOCUMENT_ROOT"];
        $log_file = $ROOT . '/fatoora/fatoora/error.log';
        curl_setopt($ch, CURLOPT_VERBOSE, true);
        curl_setopt($ch, CURLOPT_STDERR, fopen($log_file, 'w+'));

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        $response = curl_exec($ch);

        if ($response === false) {
            return 'cURL error: ' . curl_error($ch);
        }

        curl_close($ch);
        
        $this->response = $response;
        return $this->afterResponse();
    }

    public function afterResponse()
    {
        return $this->response;
    }
}

class ComplianceCSIDAPI extends FatooraApi
{
    protected $endpoint = '/compliance';
    protected $otp;
    protected $csr;

    public function setOTP($otp)
    {
        $this->otp = $otp;
    }

    public function setCSR($csr)
    {
        $this->csr = $csr;
    }

    private function getCSRContents()
    {
        $ROOT = $_SERVER["DOCUMENT_ROOT"];
        $file_path = $ROOT . '/fatoora/fatoora/generated-csr.csr';
        if (file_exists($file_path)) {
            $file_contents = file_get_contents($file_path);
            return $file_contents;
        } else {
            return "File not found.";
        }
    }

    public function setBody()
    {
        // $this->setCSR($this->getCSRContents());
        $body_array = ['csr' => $this->csr];
        $this->body = json_encode($body_array);
    }

    protected function setHeaders($additionalHeaders = [])
    {
        $additionalHeaders = [
            'OTP: ' . $this->otp,
        ];
        parent::setHeaders($additionalHeaders);
    }

    public function afterResponse()
    {
        $csidtemp = (new CSIDTemp())->processAndSaveResponse($this->response);
        return $this->response;
    }

    public function saveCertificatesToSdk($sdkPath)
    {
        $data = json_decode($this->response, true);

        // Assume $privateKeyFile and $certificateFile are the paths to the generated files
        $generatedPrivateKey = $data['secret'];
        $generatedCertificate = $data['binarySecurityToken'];

        // paths
        $certificateDirectory = $sdkPath . '/Data/Certificates';

        // Replace the contents of the original files with the generated contents
        $originalPrivateKey = $certificateDirectory . "/ec-secp256k1-priv-key.pem";
        $originalCertificate = $certificateDirectory . "/cert.pem";

        // file_put_contents($originalPrivateKey, $generatedPrivateKey);
        file_put_contents($originalCertificate, $generatedCertificate);
    }
}

class FatooraAuthApi extends FatooraApi
{
    protected $username;
    protected $password;
    protected $table;

    protected function setTable($table)
    {
        $this->table = $table;
    }

    protected function setUsernamePassword()
    {
        if ($this->table == 'Emtyaz.Fatoora.CSIDProduction') {
            $csidTemp = (new CSIDProduction())->findLastRecord($this->table);
        } else {
            $csidTemp = (new CSIDTemp())->findLastRecord($this->table);
        }
        $this->username = $csidTemp['BinarySecurityToken'];
        $this->password = $csidTemp['Secret'];
    }

    protected function getAuthorizationString()
    {
        return $this->username . ":" . $this->password;
    }

    public function postRequest()
    {
        $this->setBody();
        $this->setHeaders();
        $url = $this->base_url . $this->endpoint;

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $this->method);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $this->body);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);

        $this->setUsernamePassword();
        curl_setopt($ch, CURLOPT_USERPWD, $this->getAuthorizationString());

        $ROOT = $_SERVER["DOCUMENT_ROOT"];
        $log_file = $ROOT . '/fatoora/fatoora/error.log';
        curl_setopt($ch, CURLOPT_VERBOSE, true);
        curl_setopt($ch, CURLOPT_STDERR, fopen($log_file, 'w+'));

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        $response = curl_exec($ch);

        if ($response === false) {
            return 'cURL error: ' . curl_error($ch);
        }

        curl_close($ch);

        $this->response = $response;
        return $this->afterResponse();
    }
}

class ComplianceInvoiceAPI extends FatooraAuthApi
{
    protected $endpoint = '/compliance/invoices';
    protected $invoiceHash;
    protected $uuid;
    protected $invoice;

    public function getInvoice()
    {
        $fatooraInvoice = (new FatooraInvoice())->findInvoiceByRecID(1);
        $this->invoiceHash = $fatooraInvoice['InvoiceHash'];
        $this->uuid = $fatooraInvoice['UUID'];
        $this->invoice = $fatooraInvoice['Invoice'];
    }

    public function getInvoiceFromJson()
    {
        $file_path = '.././fatoora/api-request.json';
        $jsonContent = file_get_contents($file_path);
        $jsonData = json_decode($jsonContent, true);

        // Extracting specific variables from the JSON data
        if ($jsonData) {
            $this->invoiceHash = $jsonData['invoiceHash'] ?? null;
            $this->uuid = $jsonData['uuid'] ?? null;
            $this->invoice = $jsonData['invoice'] ?? null;
        }
    }

    public function setBody()
    {
        $this->getInvoiceFromJson();
        $body_array = ['invoiceHash' => $this->invoiceHash, 'uuid' => $this->uuid, 'invoice' => $this->invoice];
        $this->body = json_encode($body_array);
    }

    public function postRequest()
    {
        $this->setTable('Emtyaz.Fatoora.CSIDTemp');
        return parent::postRequest();
    }
}

class ProductionCSIDOnboardingAPI extends FatooraAuthApi
{
    protected $endpoint = '/production/csids';
    protected $compliance_request_id;

    public function getCSIDTemp()
    {
        $csidTemp = (new CSIDTemp())->findLastRecord($this->table);
        $this->compliance_request_id = $csidTemp['RequestID'];
    }

    public function setBody()
    {
        $this->getCSIDTemp();
        $body_array = ['compliance_request_id' => $this->compliance_request_id];
        $this->body = json_encode($body_array);
    }

    public function postRequest()
    {
        $this->setTable('Emtyaz.Fatoora.CSIDTemp');
        return parent::postRequest();
    }

    public function afterResponse()
    {
        $csidProduction = (new CSIDProduction())->processAndSaveResponse($this->response);
        return $this->response;
    }
}

class ProductionCSIDRenewalAPI extends FatooraAuthApi
{
    protected $endpoint = '/production/csids';
    protected $otp;
    protected $csr;

    public function __construct()
    {
        $this->method = 'PATCH';
        $this->table = 'Emtyaz.Fatoora.CSIDProduction';
    }

    public function setOTP($otp)
    {
        $this->otp = $otp;
    }

    public function setCSR($csr)
    {
        $this->csr = $csr;
    }

    private function getCSRContents()
    {
        $ROOT = $_SERVER["DOCUMENT_ROOT"];
        $file_path = $ROOT . '/fatoora/fatoora/generated-csr.csr';
        if (file_exists($file_path)) {
            $file_contents = file_get_contents($file_path);
            return $file_contents;
        } else {
            return "File not found.";
        }
    }

    public function setBody()
    {
        $this->setCSR($this->getCSRContents());
        $body_array = ['csr' => $this->csr];
        $this->body = json_encode($body_array);
    }

    protected function setHeaders($additionalHeaders = [])
    {
        $additionalHeaders = [
            'OTP: ' . $this->otp,
        ];
        parent::setHeaders($additionalHeaders);
    }

    public function afterResponse()
    {
        $csidProduction = (new CSIDRenewal())->processAndSaveResponse($this->response);
        return $this->response;
    }
}

class ValidationAPI extends FatooraAuthApi
{
    protected $clearance_status = '1';
    protected $invoiceHash;
    protected $uuid;
    protected $invoice;
    protected $invoiceNumber;
    protected $name = 'clearance';
    protected $responseStatusKey = 'clearanceStatus';

    public function __construct($invoiceNumber)
    {
        $this->table = 'Emtyaz.Fatoora.CSIDProduction';
        $this->invoiceNumber = $invoiceNumber;
        parent::__construct();
    }

    protected function setHeaders($additionalHeaders = [])
    {
        $additionalHeaders = [
            'Clearance-Status: ' . $this->clearance_status,
        ];
        parent::setHeaders($additionalHeaders);
    }

    public function getInvoice()
    {
        $fatooraInvoice = ($this->name == 'clearance' ? new FatooraBusinessInvoice() : new FatooraInvoice())->findInvoice($this->invoiceNumber);
        $this->invoiceHash = $fatooraInvoice['InvoiceHash'];
        $this->uuid = $fatooraInvoice['UUID'];
        $this->invoice = $fatooraInvoice['Invoice'];
    }

    public function getInvoiceFromJson()
    {
        $file_path = '.././fatoora\api-request.json';
        $jsonContent = file_get_contents($file_path);
        $jsonData = json_decode($jsonContent, true);

        // Extracting specific variables from the JSON data
        if ($jsonData) {
            $this->invoiceHash = $jsonData['invoiceHash'] ?? null;
            $this->uuid = $jsonData['uuid'] ?? null;
            $this->invoice = $jsonData['invoice'] ?? null;
        }
    }

    public function setBody()
    {
        // $this->getInvoiceFromJson();
        $this->getInvoice();
        $body_array = ['invoiceHash' => $this->invoiceHash, 'uuid' => $this->uuid, 'invoice' => $this->invoice];
        $this->body = json_encode($body_array);
    }

    public function afterResponse()
    {
        $responseAss = json_decode($this->response, true);
        $status =  $responseAss[$this->responseStatusKey] ?? null;

        if (!empty($status)) {
            $this->changeStatus($status);
        }

        return $this->response;
    }

    public function changeStatus($status)
    {
        $fatooraInvoice = $this->name == 'clearance' ? new FatooraBusinessInvoice() : new FatooraInvoice();
        $fatooraInvoice->setCreationStatus($this->invoiceNumber, 3);

        if ($status == 'NOT_CLEARED') {
            $fatooraInvoice->setReportingStatus($this->invoiceNumber, 1);
        } else if ($status == 'NOT_REPORTED') {
            $fatooraInvoice->setReportingStatus($this->invoiceNumber, 1);
        }  else {
            $fatooraInvoice->setReportingStatus($this->invoiceNumber, 1);
        }
    }
}

class ReportingAPI extends ValidationAPI
{
    protected $endpoint = '/invoices/reporting/single';
    protected $name = 'reporting';
    protected $responseStatusKey = 'reportingStatus';
}

class ClearanceAPI extends ValidationAPI
{
    protected $endpoint = '/invoices/clearance/single';
}
