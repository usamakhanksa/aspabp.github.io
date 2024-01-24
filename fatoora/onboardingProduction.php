<?php

$ROOT = $_SERVER["DOCUMENT_ROOT"];
require_once $ROOT . '/fatoora/vendor/autoload.php';
require_once $ROOT . '/fatoora/app/fatoora/FatooraApi.php';

try {
    $fatooraApi = new ProductionCSIDOnboardingAPI();
    $response = $fatooraApi->postRequest();
    echo $response;
} catch (Exception $e) {
    echo $e;
}
