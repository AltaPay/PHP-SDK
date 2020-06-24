<?php

class ValitorRequestTimeoutException extends ValitorMerchantAPIException
{
    /**
     * ValitorRequestTimeoutException constructor.
     *
     * @param $url
     */
    public function __construct($url)
    {
        parent::__construct('Request to '.$url.' timed out', 39824714);
    }
}
