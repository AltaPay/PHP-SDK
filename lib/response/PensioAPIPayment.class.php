<?php

if(!defined('PENSIO_API_ROOT'))
{
	define('PENSIO_API_ROOT',dirname(__DIR__));
}

require_once(PENSIO_API_ROOT.'/response/PensioAPIReconciliationIdentifier.class.php');

/**
   [Transaction] =&gt; SimpleXMLElement Object
       (
           [TransactionId] =&gt; 5
           [AuthType] =&gt; payment
           [CardStatus] =&gt; Valid
           [CreditCardToken] =&gt; ce657182528301c19032840ba6682bdeb5b342d8
           [CreditCardMaskedPan] =&gt; 555555*****5444
           [ThreeDSecureResult] =&gt; Not_Attempted
           [BlacklistToken] =&gt; 9484bac14dfd5dbb27329f81dcb12ceb8ed7703e
           [ShopOrderId] =&gt; qoute_247
           [Shop] =&gt; Pensio Functional Test Shop
           [Terminal] =&gt; Pensio Dev Terminal
           [TransactionStatus] =&gt; preauth
           [MerchantCurrency] =&gt; 978
           [CardHolderCurrency] =&gt; 978
           [ReservedAmount] =&gt; 14.10
           [CapturedAmount] =&gt; 0
           [RefundedAmount] =&gt; 0
           [RecurringMaxAmount] =&gt; 0
           [CreatedDate] =&gt; 2012-01-06 15:23:12
           [UpdatedDate] =&gt; 2012-01-06 15:23:12
           [PaymentNature] =&gt; CreditCard
           [PaymentSource] = &gt; eCommerce
           [PaymentNatureService] =&gt; SimpleXMLElement Object
               (
                   [@attributes] =&gt; Array
                       (
                           [name] =&gt; TestAcquirer
                       )

                   [SupportsRefunds] =&gt; true
                   [SupportsRelease] =&gt; true
                   [SupportsMultipleCaptures] =&gt; true
                   [SupportsMultipleRefunds] =&gt; true
               )

           [FraudRiskScore] =&gt; 14
           [FraudExplanation] =&gt; For the test fraud service the risk score is always equal mod 101 of the created amount for the payment
           [TransactionInfo] =&gt; SimpleXMLElement Object
               (
               )

           [CustomerInfo] =&gt; SimpleXMLElement Object
               (
                   [UserAgent] =&gt; SimpleXMLElement Object
                       (
                       )

                   [IpAddress] =&gt; 127.0.0.1
               )

           [ReconciliationIdentifiers] =&gt; SimpleXMLElement Object
               (
               )
 * @author emanuel
 */
class PensioAPIPayment
{
	private $simpleXmlElement;

	// Remember to reflect additions within this->getCurrentXml()
	private $transactionId;
	private $uuid;
	private $authType;
	private $creditCardMaskedPan;
	private $creditCardExpiryMonth;
	private $creditCardExpiryYear;
	private $creditCardToken;
	private $cardStatus;
	private $shopOrderId;
	private $shop;
	private $terminal;
	private $transactionStatus;
	private $reasonCode;
	private $currency;
	private $addressVerification;
	private $addressVerificationDescription;
	
	private $reservedAmount;
	private $capturedAmount;
	private $refundedAmount;
	private $recurringMaxAmount;
	private $surchargeAmount;

	private $paymentSchemeName;
	private $paymentNature;
	private $paymentSource;
	private $paymentNatureService;

	private $fraudRiskScore;
	private $fraudExplanation;
	private $fraudRecommendation;
	// Remember to reflect additions within this->getCurrentXml()

	/**
	 * @var PensioAPICustomerInfo
	 */
	private $customerInfo;
	
	/**
	 * @var PensioAPIPaymentInfos
	 */
	private $paymentInfos;
	
	private $reconciliationIdentifiers = array();

	/**
	 * @var PensioAPIChargebackEvents
	 */
	private $chargebackEvents;
	// Remember to reflect additions within this->getCurrentXml()
	
	public function __construct(SimpleXmlElement $xml)
	{
		$this->simpleXmlElement = $xml->saveXML();
		$this->transactionId = (string)$xml->TransactionId;
		$this->uuid = (string)$xml->PaymentId;
		$this->authType = (string)$xml->AuthType;
		$this->creditCardMaskedPan = (string)$xml->CreditCardMaskedPan;
		$this->creditCardExpiryMonth = (string)$xml->CreditCardExpiry->Month;
		$this->creditCardExpiryYear = (string)$xml->CreditCardExpiry->Year;
		$this->creditCardToken = (string)$xml->CreditCardToken;
		$this->cardStatus = (string)$xml->CardStatus;
		$this->shopOrderId = (string)$xml->ShopOrderId;
		$this->shop = (string)$xml->Shop;
		$this->terminal = (string)$xml->Terminal;
		$this->transactionStatus = (string)$xml->TransactionStatus;
		$this->reasonCode = (string)$xml->ReasonCode;
		$this->currency = (string)$xml->MerchantCurrency;
		$this->addressVerification = (string)$xml->AddressVerification;
		$this->addressVerificationDescription = (string)$xml->AddressVerificationDescription;

		$this->reservedAmount = (string)$xml->ReservedAmount;
		$this->capturedAmount = (string)$xml->CapturedAmount;
		$this->refundedAmount = (string)$xml->RefundedAmount;
		$this->recurringMaxAmount = (string)$xml->RecurringMaxAmount;
		$this->surchargeAmount = (String)$xml->SurchargeAmount;
		
		$this->paymentSchemeName = (string)$xml->PaymentSchemeName;
		$this->paymentNature = (string)$xml->PaymentNature;
		$this->paymentSource = (string)$xml->PaymentSource;
		$this->paymentNatureService = new PensioAPIPaymentNatureService($xml->PaymentNatureService);

		$this->fraudRiskScore = (string)$xml->FraudRiskScore;
		$this->fraudExplanation = (string)$xml->FraudExplanation;
		$this->fraudRecommendation = (string)$xml->FraudRecommendation;

		$this->customerInfo = new PensioAPICustomerInfo($xml->CustomerInfo);
		$this->paymentInfos = new PensioAPIPaymentInfos($xml->PaymentInfos);
		$this->chargebackEvents = new PensioAPIChargebackEvents($xml->ChargebackEvents);

		if(isset($xml->ReconciliationIdentifiers->ReconciliationIdentifier))
		{
			foreach($xml->ReconciliationIdentifiers->ReconciliationIdentifier as $reconXml)
			{
				$this->reconciliationIdentifiers[] = new PensioAPIReconciliationIdentifier($reconXml);
			}
		}
	}
	
	public function mustBeCaptured()
	{
		return $this->capturedAmount == '0';
	}
	
	public function getCurrentStatus()
	{
		return $this->transactionStatus;
	}
	
	public function isReleased()
	{
		return $this->getCurrentStatus() == 'released';
	}
	
	/**
	 * @return PensioAPIReconciliationIdentifier
	 */
	public function getLastReconciliationIdentifier()
	{
		return $this->reconciliationIdentifiers[count($this->reconciliationIdentifiers) - 1];
	}
	
	public function getId()
	{
		return $this->transactionId;
	}

	public function getPaymentId()
	{
		return $this->uuid;
	}
	
	public function getAuthType()
	{
		return $this->authType;
	}
	
	public function getShopOrderId()
	{
		return $this->shopOrderId;
	}
	
	public function getMaskedPan()
	{
		return $this->creditCardMaskedPan;
	}

	public function getCreditCardExpiryMonth()
	{
		return $this->creditCardExpiryMonth;
	}

	public function getCreditCardExpiryYear()
	{
		return $this->creditCardExpiryYear;
	}

	public function getCreditCardToken()
	{
		return $this->creditCardToken;
	}
	
	public function getCardStatus()
	{
		return $this->cardStatus;
	}
	
	public function getPaymentNature()
	{
		return $this->paymentNature;
	}

	public function getPaymentSource()
	{
		return $this->paymentSource;
	}
	
	public function getPaymentSchemeName()
	{
		return $this->paymentSchemeName;
	}
	
	/**
	 * @return PensioAPIPaymentNatureService
	 */
	public function getPaymentNatureService()
	{
		return $this->paymentNatureService;
	}

	/**
	 * @return string
	 */
	public function getFraudRiskScore()
	{
		return $this->fraudRiskScore;
	}

	/**
	 * @return string
	 */
	public function getFraudExplanation()
	{
		return $this->fraudExplanation;
	}

	/**
	 * @return string
	 */
	public function getFraudRecommendation()
	{
		return $this->fraudRecommendation;
	}

	/**
	 * @return PensioAPICustomerInfo
	 */
	public function getCustomerInfo()
	{
		return $this->customerInfo;
	}
	
	public function getPaymentInfo($keyName)
	{
		return $this->paymentInfos->getInfo($keyName);
	}

	public function getReasonCode()
	{
		return $this->reasonCode;
	}

	public function getCurrency()
	{
		return $this->currency;
	}
	
	public function getReservedAmount()
	{
		return $this->reservedAmount;
	}
	
	public function getCapturedAmount()
	{
		return $this->capturedAmount; 
	}
	
	public function getRefundedAmount()
	{
		return $this->refundedAmount;
	}

	/**
	 * @return PensioAPIChargebackEvents
	 */
	public function getChargebackEvents()
	{
		return $this->chargebackEvents;
	}

	/**
	 * Returns an XML representation of the payment as used to instantiate the object. It does not reflect any subsequent changes.
	 * @see PensioAPIPayment::getCurrentXml() for an up-to-date XML representation of the payment
	 * @return SimpleXMLElement an XML representation of the object as it was instantiated
	 */
	public function getXml()
	{
		return $this->simpleXmlElement;
	}
	
	/**
	 * Returns an up-to-date XML representation of the payment
	 * @see PensioAPIPayment::getXml() for an XML representation of the payment as used to instantiate the object
	 * @return SimpleXMLElement an up-to-date XML representation of the payment
	 */
	public function getCurrentXml()
	{
		$simpleXmlElement = new SimpleXMLElement('<PensioAPIPayment></PensioAPIPayment>');
		
		$simpleXmlElement->addChild('transactionId', $this->transactionId);
		$simpleXmlElement->addChild('uuid', $this->uuid);
		$simpleXmlElement->addChild('authType', $this->authType);
		$simpleXmlElement->addChild('creditCardMaskedPan', $this->creditCardMaskedPan);
		$simpleXmlElement->addChild('creditCardExpiryMonth', $this->creditCardExpiryMonth);
		$simpleXmlElement->addChild('creditCardExpiryYear', $this->creditCardExpiryYear);
		$simpleXmlElement->addChild('creditCardToken', $this->creditCardToken);
		$simpleXmlElement->addChild('cardStatus', $this->cardStatus);
		$simpleXmlElement->addChild('shopOrderId', $this->shopOrderId);
		$simpleXmlElement->addChild('shop', $this->shop);
		$simpleXmlElement->addChild('terminal', $this->terminal);
		$simpleXmlElement->addChild('transactionStatus', $this->transactionStatus);
		$simpleXmlElement->addChild('reasonCode', $this->reasonCode);
		$simpleXmlElement->addChild('currency', $this->currency);
		$simpleXmlElement->addChild('addressVerification', $this->addressVerification);
		$simpleXmlElement->addChild('addressVerificationDescription', $this->addressVerificationDescription);
		
		$simpleXmlElement->addChild('reservedAmount', $this->reservedAmount);
		$simpleXmlElement->addChild('capturedAmount', $this->capturedAmount);
		$simpleXmlElement->addChild('refundedAmount', $this->refundedAmount);
		$simpleXmlElement->addChild('recurringMaxAmount', $this->recurringMaxAmount);
		$simpleXmlElement->addChild('surchargeAmount', $this->surchargeAmount);
	
		$simpleXmlElement->addChild('paymentSchemeName', $this->paymentSchemeName);
		$simpleXmlElement->addChild('paymentNature', $this->paymentNature);
		$simpleXmlElement->addChild('paymentSource', $this->paymentSource);
		$simpleXmlElement->addChild('paymentNatureService', $this->paymentNatureService);
	
		$simpleXmlElement->addChild('fraudRiskScore', $this->fraudRiskScore);
		$simpleXmlElement->addChild('fraudExplanation', $this->fraudExplanation);
		$simpleXmlElement->addChild('fraudRecommendation', $this->fraudRecommendation);
	
		$simpleXmlElement->addChild('PensioAPICustomerInfo', $this->customerInfo->getXmlElement());
		$simpleXmlElement->addChild('PensioAPIPaymentInfos', $this->paymentInfos->getXmlElement());
		$simpleXmlElement->addChild('PensioAPIChargebackEvents', $this->chargebackEvents->getXmlElement());
	
		return $simpleXmlElement;
	}

	public function getSurchargeAmount(){
		return $this->surchargeAmount;
	}

	public function getTerminal(){
		return $this->terminal;
	}

	/**
	 * Gives the amount of the good(s) without surcharge.
	 */
	public function getInitiallyAmount(){
		return bcsub($this->reservedAmount, $this->surchargeAmount, 2);
	}

	public function getAddressVerification()
	{
		return $this->addressVerification;
	}

	public function getAddressVerificationDescription()
	{
		return $this->addressVerificationDescription;
	}
}