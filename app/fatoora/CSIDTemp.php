<?php

$ROOT = $_SERVER["DOCUMENT_ROOT"];
require_once $ROOT . '/fatoora/vendor/autoload.php';
require_once $ROOT . "/fatoora/app/fatoora/CSID.php";

class CSIDTemp extends CSID
{
    public function __construct() {
        $this->table = 'Emtyaz.Fatoora.CSIDTemp';
    }
}
