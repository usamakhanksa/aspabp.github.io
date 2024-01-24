<?php

namespace Objects\Client;

use Saleh7\Zatca\Address;
use Saleh7\Zatca\LegalEntity;
use Saleh7\Zatca\Party;
use Saleh7\Zatca\PartyTaxScheme;
use Saleh7\Zatca\TaxScheme;

class ClientB2C extends Party
{
    protected PartyTaxScheme $taxScheme;
    protected Address $address;
    protected LegalEntity $legalEntity;

    public function __construct(TaxScheme $taxScheme, $registrationName, $VATNumber = null, $registrationNumber = null, $registrationNumberType = 'CRN', $streetName = null, $buildingNumber = null, $plotIdentification = null, $citySubdivisionName = null, $cityName = null, $postalZone = null, $country = null)
    {
        if ($VATNumber == null || empty($VATNumber) || strlen($VATNumber) != 15 || substr($VATNumber, 0, 1) != 3 || substr($VATNumber, -1)) {
            $this->taxScheme = (new PartyTaxScheme)->setTaxScheme($taxScheme);
        } else {
            $this->taxScheme = (new PartyTaxScheme)->setTaxScheme($taxScheme)->setCompanyId($VATNumber);
        }

        $this->address = (new Address);
        !empty($streetName) ? $this->address->setStreetName($streetName) : null;
        !empty($buildingNumber) ? $this->address->setBuildingNumber($buildingNumber) : null;
        !empty($plotIdentification) ? $this->address->setPlotIdentification($plotIdentification) : null;
        !empty($citySubdivisionName) ? $this->address->setCitySubdivisionName($citySubdivisionName) : null;
        !empty($cityName) ? $this->address->setCityName($cityName) : null;
        !empty($postalZone) ? $this->address->setPostalZone($postalZone) : null;
        !empty($country) ? $this->address->setCountry($country) : null;

        $this->legalEntity = (new LegalEntity)->setRegistrationName($registrationName);

        !empty($registrationNumber) ? $this->setPartyIdentification($registrationNumber) : null;
        !empty($registrationNumber) ? $this->setPartyIdentificationId($registrationNumberType) : null;
        
        $this->setLegalEntity($this->legalEntity);
        $this->setPartyTaxScheme($this->taxScheme);
        $this->setPostalAddress($this->address);
    }
}