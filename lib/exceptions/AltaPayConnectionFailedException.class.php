<?php

class AltaPayConnectionFailedException extends AltaPayMerchantAPIException
{
    /**
     * @param string $url
     * @param string $reason
     */
    public function __construct($url, $reason)
    {
        parent::__construct('Connection to '.$url.' failed (reason: '.$reason.')', 23483431);
    }
}