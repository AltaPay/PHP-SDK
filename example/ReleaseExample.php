<?php
require_once __DIR__.'/base.php';
$api = InitializeAltapayMerchantAPI();
$terminal = 'Some Terminal'; // change this to one of the test terminals supplied in the welcome email

// Different variables which are used as arguments
$amount = 125.55;
$orderLines = array(
    array(
        'description' => 'Item1',
        'itemId'      => 'Item1',
        'quantity'    => 5,
        'unitPrice'   => 12.0,
        'taxAmount'   => 0.0,
        'taxPercent'  => 0.0,
        'unitCode'    => 'g',
        'goodsType'   => 'item',
    ),
    array(
        'description' => 'Item2',
        'itemId'      => 'Item2',
        'quantity'    => 2,
        'unitPrice'   => 15.0,
        'taxAmount'   => 0.0,
        'taxPercent'  => 0.0,
        'unitCode'    => 'g',
        'goodsType'   => 'item',
    ),
    array(
        'description' => 'Shipping fee',
        'itemId'      => 'ShippingItem',
        'quantity'    => 1,
        'unitPrice'   => 5,
        'goodsType'   => 'shipping',
    ),
);
$transactionId = reserveAmount($api, $terminal, $amount, $orderLines);

$response = $api->releaseReservation($transactionId);
if (!$response->wasSuccessful()) {
    throw new Exception('Release operation failed: '.$response->getErrorMessage());
}
echo 'Successful release';
