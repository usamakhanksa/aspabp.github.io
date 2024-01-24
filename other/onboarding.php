<?php

$ROOT = $_SERVER["DOCUMENT_ROOT"];
require_once $ROOT . '/fatoora/vendor/autoload.php';
require_once $ROOT . '/fatoora/fatoora/csr-config.php';
require_once $ROOT . '/fatoora/app/fatoora/FatooraSettings.php';

use Bl\FatooraZatca;
use Bl\FatooraZatca\Objects\Client;
use Bl\FatooraZatca\Objects\Invoice;
use Bl\FatooraZatca\Objects\Seller;
use Bl\FatooraZatca\Objects\Setting;
use Bl\FatooraZatca\Zatca;
use My\Fatoora\Config;

$zatca = new Zatca();

$otp = $_GET['otp'];

$settings = new Setting($otp, 'hmmdlthf@gmail.com', 'TST-886431145-300066889400003', '0542936608', $supplierName, $supplierVAT, $supplierStreetName, 'Grocery Catering', invoiceType:'1111');
$settings = $zatca->generateZatcaSetting($settings);
(new FatooraSettings())->saveSettings($settings);
echo 'Onboarding Done and saved';


