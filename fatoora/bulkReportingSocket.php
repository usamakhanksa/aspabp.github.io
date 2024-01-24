<?php

use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;

$ROOT = $_SERVER["DOCUMENT_ROOT"];
require_once $ROOT . '/fatoora/vendor/autoload.php';
require_once $ROOT . '/fatoora/app/fatoora/FatooraInvoice.php';
require_once $ROOT . '/fatoora/app/fatoora/FatooraSocket.php';
require_once $ROOT . '/fatoora/app/fatoora/FatooraApi.php';
require_once $ROOT . '/fatoora/app/fatoora/utils/Utils.php';
require_once $ROOT . '/fatoora/fatoora/utils.php';

// set the header to application json
// header('Content-Type: application/json');

// Create WebSocket server
$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new FatooraWebSocket()
        )
    ),
    8089 // Port number
);

$websocket = $server->getApplication()->getApp();


// implement the invoice reporting
$fatooraInvoice = new FatooraInvoice();
$notReportedArray = $fatooraInvoice->findAllInvoiceNotReported();

$message = '';
foreach($notReportedArray as $notReportedInvoice) {
    $invoiceNumber = $notReportedInvoice['InvoiceNumber'];
    // run the reporting invoice api
    $fatooraApi = new ReportingAPI($invoiceNumber);
    $response = $fatooraApi->postRequest();

    // check if the reporting was a sucess else break
    $result = $fatooraInvoice->findInvoiceStatus($invoiceNumber);
    if ($result['ReportingStatusRecID'] != 1) {
        break;
    } else {
        $message = "Reported Invoice: $invoiceNumber";
        $websocket->sendToAll($message);
    }
}

$server->run();