<?php

require_once(dirname(__FILE__).'/PensioAbstractPaymentResponse.class.php');

class PensioReleaseResponse extends PensioAbstractPaymentResponse
{
	public function __construct(SimpleXmlElement $xml)
	{
		parent::__construct($xml);
	}
	
	protected function parseBody(SimpleXmlElement $body)
	{
		
	}
	
}