<?php

class AltapayAPIChargebackEvents
{
    /** @var SimpleXMLElement */
    private $simpleXmlElement;
    /** @var AltapayAPIChargebackEvent[] */
    private $chargebackEvents = array();

    /**
     * @param SimpleXMLElement $xml
     *
     * @throws Exception
     */
    public function __construct(SimpleXMLElement $xml)
    {
        $this->simpleXmlElement = $xml;
        if (isset($xml->ChargebackEvent)) {
            foreach ($xml->ChargebackEvent as $chargebackEvent) {
                $this->chargebackEvents[] = new AltapayAPIChargebackEvent($chargebackEvent);
            }
        }
    }

    /**
     * @return AltapayAPIChargebackEvent|null
     */
    public function getNewest()
    {
        $newest = null;
        foreach ($this->chargebackEvents as $chargebackEvent) {
            if ($newest === null || $newest->getDate()->getTimestamp() < $chargebackEvent->getDate()->getTimestamp()) {
                $newest = $chargebackEvent;
            }
        }

        return $newest;
    }

    /**
     * @return SimpleXMLElement an XML representation of the object as it was instantiated
     */
    public function getXmlElement()
    {
        return $this->simpleXmlElement;
    }
}
