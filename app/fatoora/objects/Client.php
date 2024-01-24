<?php

namespace Objects\Client;

use Saleh7\Zatca\Address;
use Saleh7\Zatca\LegalEntity;
use Saleh7\Zatca\Party;
use Saleh7\Zatca\PartyTaxScheme;
use Saleh7\Zatca\TaxScheme;

class Client extends Party
{
    protected PartyTaxScheme $taxScheme;
    protected Address $address;
    protected LegalEntity $legalEntity;

    public function __construct(TaxScheme $taxScheme, $registrationName, $VATNumber, $registrationNumber, $registrationNumberType, $streetName, $buildingNumber, $plotIdentification, $citySubdivisionName, $cityName, $postalZone, $country)
    {
        $this->taxScheme = (new PartyTaxScheme)->setTaxScheme($taxScheme)->setCompanyId($VATNumber);
        $this->address = (new Address)
            ->setStreetName($streetName)
            ->setBuildingNumber($buildingNumber)
            ->setPlotIdentification($plotIdentification)
            ->setCitySubdivisionName($citySubdivisionName)
            ->setCityName($cityName)
            ->setPostalZone($postalZone)
            ->setCountry($country);
        $this->legalEntity = (new LegalEntity)->setRegistrationName($registrationName);
        $this->setPartyIdentification($registrationNumber);
        $this->setPartyIdentificationId($registrationNumberType);
        $this->setLegalEntity($this->legalEntity);
        $this->setPartyTaxScheme($this->taxScheme);
        $this->setPostalAddress($this->address);
    }
}