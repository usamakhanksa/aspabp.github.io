<?php

$ROOT = $_SERVER["DOCUMENT_ROOT"];
require_once $ROOT . '/fatoora/vendor/autoload.php';
require_once $ROOT . "/fatoora/app/fatoora/FatooraInvoice.php";

class FatooraBusinessInvoice extends FatooraInvoice
{
    protected $table = 'Fatoora.BusinessInvoice';
}
