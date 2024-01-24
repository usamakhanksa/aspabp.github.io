<?php

$ROOT = $_SERVER["DOCUMENT_ROOT"];
require_once $ROOT . '/fatoora/vendor/autoload.php';
require_once $ROOT . "/fatoora/app/constants/Constants.php";

function getModeByProductSourceRecID($productSourceRecID)
{
    return $productSourceRecID == 2 ? InventoryModes::SHOWROOM : InventoryModes::WAREHOUSE;
}

function getProductSourceRecIDByMode($mode)
{
    return $mode == InventoryModes::SHOWROOM ? 2 : 1;
}

function getTableNameByMode($mode)
{
    return $mode == InventoryModes::WAREHOUSE ? '[V_ProductRetail_InventoryW]' : '[V_ProductRetail_InventoryR]';
}

function getRecIDColumnName($mode)
{
    return $mode == InventoryModes::WAREHOUSE ? '[RecID]' : '[ProductRecID]';
}

function getNextProductSourceRecID($productSourceRecID)
{
    return $productSourceRecID == 1 ? 2 : 1; 
}

function getNextProductSourceRecIDByMode($mode)
{
    return $mode == InventoryModes::WAREHOUSE ? 2 : 1; 
}