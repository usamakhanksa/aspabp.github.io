<?php

$ROOT = $_SERVER["DOCUMENT_ROOT"];
require_once $ROOT . '/fatoora/vendor/autoload.php';
require_once $ROOT . "/fatoora/app/fatoora/CSID.php";

class CSIDRenewal extends CSID
{
    public function __construct() {
        $this->table = 'Emtyaz.Fatoora.CSIDProduction';
    }

    public function processAndSaveResponse($responseData)
    {
        $data = json_decode($responseData, true);

        // Extracting individual data elements from the decoded JSON
        $requestID = $data['requestID'];
        $binarySecurityToken = $data['binarySecurityToken'];
        // $requestedSecurityToken = $data['RequestedSecurityToken'];
        $secret = $data['secret'];

        // Getting current date
        $currentDate = date('Y-m-d H:i:s');
        // Calculating 7 days from now
        $expireDate = date('Y-m-d H:i:s', strtotime('+7 days'));

        $this->create($requestID, $binarySecurityToken, $secret, $currentDate, $expireDate);
        return true;
    }
}
