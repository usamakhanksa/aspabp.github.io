<?php

namespace Objects\Supplier;

$ROOT = $_SERVER["DOCUMENT_ROOT"];
require_once $ROOT . '/fatoora/vendor/autoload.php';
require_once $ROOT . "/fatoora/app/fatoora/objects/Client.php";

use Objects\Client\Client;
use Saleh7\Zatca\Address;
use Saleh7\Zatca\LegalEntity;
use Saleh7\Zatca\Party;
use Saleh7\Zatca\PartyTaxScheme;
use Saleh7\Zatca\TaxScheme;

class Supplier extends Client
{
    
}