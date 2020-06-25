<?php

use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;

class ValitorReservationTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /** @var TestConfig */
    private $config;
    /** @var ValitorMerchantAPI */
    private $merchantApi;

    /**
     * @throws ValitorMerchantAPIException
     */
    protected function setUp(): void
    {
        $this->config = new TestConfig();
        $this->merchantApi = new ValitorMerchantAPI($this->config->installation, $this->config->username, $this->config->password);
        $this->merchantApi->login();
    }

    public function testSuccessfulReservation(): void
    {
        $response = $this->merchantApi->reservation(
            $this->config->terminal,
            'ReservationTest_'.time(),
            42.00,
            $this->config->currency,
            null,
            '4111000011110000',
            '1',
            '2018',
            '123'
        );

        static::assertTrue($response->wasSuccessful());
    }

    public function testFailedReservation(): void
    {
        $response = $this->merchantApi->reservation(
            $this->config->terminal,
            'ReservationTest_'.time(),
            5.66,
            $this->config->currency,
            null,
            '4111000011110000',
            '1',
            '2018',
            '123'
        );

        static::assertTrue($response->wasDeclined());
    }

    public function testErroneousReservation(): void
    {
        $response = $this->merchantApi->reservation(
            $this->config->terminal,
            'ReservationTest_'.time(),
            5.67,
            $this->config->currency,
            null,
            '4111000011110000',
            '1',
            '2018',
            '123'
        );

        static::assertTrue($response->wasErroneous());
    }

    public function testSuccessfulReservationUsingToken(): void
    {
        $response = $this->merchantApi->reservation(
            $this->config->terminal,
            'ReservationTest_'.time(),
            42.00,
            $this->config->currency,
            null,
            '4111000011110000',
            '1',
            '2018',
            '123'
        );

        $response2 = $this->merchantApi->reservation(
            $this->config->terminal,
            'ReservationTest_'.time(),
            42.00,
            $this->config->currency,
            $response->getPrimaryPayment()->getCreditCardToken(),
            null,
            null,
            null,
            '123'
        );

        static::assertTrue($response2->wasSuccessful());
    }

    public function testReservationUsingAllParameters(): void
    {
        $orderLines = array(
            array('description' => 'SomeDescription', 'itemId' => 'KungFuBoy', 'quantity' => 1.00, 'unitPrice' => 21.12, 'taxAmount' => 0.00, 'unitCode' => 'kg', 'discount' => 0.00, 'goodsType' => 'item'),
            array('description' => 'SomeDescription', 'itemId' => 'KarateKid', 'quantity' => 1.00, 'unitPrice' => 21.12, 'taxAmount' => 0.00, 'unitCode' => 'kg', 'discount' => 0.00, 'goodsType' => 'item'),
        );

        $customerInfo = array(
            'billing_postal'     => '1111',
            'billing_country'    => 'DK', // 2 character ISO-3166
            'billing_address'    => 'bil address 1',
            'billing_city'       => 'bil city',
            'billing_region'     => 'bil region',
            'billing_firstname'  => 'bil name',
            'billing_lastname'   => 'bil last',
            'shipping_postal'    => '2222',
            'shipping_country'   => 'BR', // 2 character ISO-3166
            'shipping_address'   => 'ship address 1',
            'shipping_city'      => 'ship city',
            'shipping_region'    => 'ship region',
            'shipping_firstname' => 'ship name',
            'shipping_lastname'  => 'ship last',
            'email'              => 'testperson@mydomain.com',
            'username'           => 'user name',
            'customer_phone'     => '11 22 33 44',
        );

        $shop_orderid = 'ReservationTest_'.time();

        $response = $this->merchantApi->reservation(
            $this->config->terminal,
            $shop_orderid,
            42.00,
            $this->config->currency,
            null,
            '4111000011110000',
            '1',
            '2018',
            '123',
            array('info1' => 'desc1', 'info2' => 'desc2'),
            'paymentAndCapture',
            'mail_order',
            'test',
            2.5,
            '2017-12-30',
            'International',
            $customerInfo,
            $orderLines
        );

        static::assertTrue($response->wasSuccessful(), $response->getErrorMessage());

        static::assertEquals($this->config->terminal, $response->getPrimaryPayment()->getTerminal());
        static::assertEquals($shop_orderid, $response->getPrimaryPayment()->getShopOrderId());
        static::assertEquals('paymentAndCapture', $response->getPrimaryPayment()->getAuthType());
        static::assertEquals(42, $response->getPrimaryPayment()->getCapturedAmount());
        static::assertEquals(42, $response->getPrimaryPayment()->getReservedAmount());
        static::assertEquals('411100******0000', $response->getPrimaryPayment()->getMaskedPan());
        static::assertEquals(1, $response->getPrimaryPayment()->getCreditCardExpiryMonth());
        static::assertEquals(2018, $response->getPrimaryPayment()->getCreditCardExpiryYear());

        static::assertEquals('1111', $response->getPrimaryPayment()->getCustomerInfo()->getBillingAddress()->getPostalCode());
        static::assertEquals('DK', $response->getPrimaryPayment()->getCustomerInfo()->getBillingAddress()->getCountry());
        static::assertEquals('bil address 1', $response->getPrimaryPayment()->getCustomerInfo()->getBillingAddress()->getAddress());
        static::assertEquals('bil city', $response->getPrimaryPayment()->getCustomerInfo()->getBillingAddress()->getCity());
        static::assertEquals('bil region', $response->getPrimaryPayment()->getCustomerInfo()->getBillingAddress()->getRegion());
        static::assertEquals('bil name', $response->getPrimaryPayment()->getCustomerInfo()->getBillingAddress()->getFirstName());
        static::assertEquals('bil last', $response->getPrimaryPayment()->getCustomerInfo()->getBillingAddress()->getLastName());

        static::assertEquals('2222', $response->getPrimaryPayment()->getCustomerInfo()->getShippingAddress()->getPostalCode());
        static::assertEquals('BR', $response->getPrimaryPayment()->getCustomerInfo()->getShippingAddress()->getCountry());
        static::assertEquals('ship address 1', $response->getPrimaryPayment()->getCustomerInfo()->getShippingAddress()->getAddress());
        static::assertEquals('ship city', $response->getPrimaryPayment()->getCustomerInfo()->getShippingAddress()->getCity());
        static::assertEquals('ship region', $response->getPrimaryPayment()->getCustomerInfo()->getShippingAddress()->getRegion());
        static::assertEquals('ship name', $response->getPrimaryPayment()->getCustomerInfo()->getShippingAddress()->getFirstName());
        static::assertEquals('ship last', $response->getPrimaryPayment()->getCustomerInfo()->getShippingAddress()->getLastName());

        static::assertEquals('testperson@mydomain.com', $response->getPrimaryPayment()->getCustomerInfo()->getEmail());
        static::assertEquals('user name', $response->getPrimaryPayment()->getCustomerInfo()->getUsername());
        static::assertEquals('11 22 33 44', $response->getPrimaryPayment()->getCustomerInfo()->getPhone());

        static::assertEquals('desc1', $response->getPrimaryPayment()->getPaymentInfo('info1'));
        static::assertEquals('desc2', $response->getPrimaryPayment()->getPaymentInfo('info2'));
    }
}