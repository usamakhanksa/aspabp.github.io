<?php

$ROOT = $_SERVER["DOCUMENT_ROOT"];
require_once $ROOT . '/fatoora/vendor/autoload.php';
require_once $ROOT . "/fatoora/app/database/Db.php";

class FatooraSettings extends Db
{
    public function saveSettings($settings)
    {
        $query = "INSERT INTO 
        .FatooraSettings 
        (cnf, private_key, public_key, csr, cert_production, secret_production, csid_id_production, cert_compliance, secret_compliance, csid_id_compliance)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $statement = $this->connect()->prepare($query);
        $statement->execute([
            $settings->cnf, 
            $settings->private_key, 
            $settings->public_key, 
            $settings->csr, 
            $settings->cert_production, 
            $settings->secret_production, 
            $settings->csid_id_production, 
            $settings->cert_compliance,
            $settings->secret_compliance, 
            $settings->csid_id_compliance
        ]);
        return true;
    }

    public function findSettings()
    {
        $query = "SELECT
        private_key,
        public_key,
        cert_production,
        secret_production,
        csid_id_production 
        FROM
        (SELECT TOP 1 private_key, public_key, cert_production, secret_production, csid_id_production FROM Fatoora.FatooraSettings ORDER BY id DESC ) AS LastRecord";

        $statement = $this->connect()->prepare($query);
        $statement->execute();
        $result = $statement->fetch();
        return $result;
    }
}