<?php

/**
 * This class represents the following data structure.
 *
 * <UserAgent>Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:13.0)
 *     Gecko/20100101 Firefox/13.0.1</UserAgent>
 * <IpAddress>81.7.175.18</IpAddress>
 * <Email></Email>
 * <Username></Username>
 * <CustomerPhone></CustomerPhone>
 * <OrganisationNumber></OrganisationNumber>
 * <CountryOfOrigin>
 *     <Country></Country><Source>NotSet</Source>
 * </CountryOfOrigin>
 */
class ValitorAPICustomerInfo
{
    /** @var SimpleXMLElement */
    private $simpleXmlElement;
    /** @var string */
    private $userAgent;
    /** @var string */
    private $ipAddress;
    /** @var string */
    private $email;
    /** @var string */
    private $username;
    /** @var string */
    private $phone;
    /** @var string */
    private $organisationNumber;

    /** @var ValitorAPIAddress */
    private $billingAddress;
    /** @var ValitorAPIAddress */
    private $shippingAddress;
    /** @var ValitorAPIAddress */
    private $registeredAddress;

    /** @var ValitorAPICountryOfOrigin */
    private $countryOfOrigin;

    /**
     * @param SimpleXMLElement $xml
     */
    public function __construct(SimpleXMLElement $xml)
    {
        $this->simpleXmlElement = $xml;
        $this->userAgent = (string)$xml->UserAgent;
        $this->ipAddress = (string)$xml->IpAddress;
        $this->email = (string)$xml->Email;
        $this->username = (string)$xml->Username;
        $this->phone = (string)$xml->CustomerPhone;
        $this->organisationNumber = (string)$xml->OrganisationNumber;

        if (isset($xml->CountryOfOrigin)) {
            $this->countryOfOrigin = new ValitorAPICountryOfOrigin($xml->CountryOfOrigin);
        }
        if (isset($xml->BillingAddress)) {
            $this->billingAddress = new ValitorAPIAddress($xml->BillingAddress);
        }
        if (isset($xml->ShippingAddress)) {
            $this->shippingAddress = new ValitorAPIAddress($xml->ShippingAddress);
        }
        if (isset($xml->RegisteredAddress)) {
            $this->registeredAddress = new ValitorAPIAddress($xml->RegisteredAddress);
        }
    }

    /**
     * @return ValitorAPIAddress
     */
    public function getBillingAddress()
    {
        return $this->billingAddress;
    }

    /**
     * @return ValitorAPIAddress
     */
    public function getShippingAddress()
    {
        return $this->shippingAddress;
    }

    /**
     * @return ValitorAPIAddress
     */
    public function getRegisteredAddress()
    {
        return $this->registeredAddress;
    }

    /**
     * @return ValitorAPICountryOfOrigin
     */
    public function getCountryOfOrigin()
    {
        return $this->countryOfOrigin;
    }

    /**
     * @return string
     */
    public function getUserAgent()
    {
        return $this->userAgent;
    }

    /**
     * @return string
     */
    public function getIpAddress()
    {
        return $this->ipAddress;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @return string
     */
    public function getOrganisationNumber()
    {
        return $this->organisationNumber;
    }

    /**
     * @return SimpleXMLElement an XML representation of the object as it was instantiated
     */
    public function getXmlElement()
    {
        return $this->simpleXmlElement;
    }
}