<?php
require_once __DIR__.'/base.php';
$api = InitializeAltapayMerchantAPI();

// Different variables, which are used as arguments
// Replace the value with a payment ID from a previous created order
$paymentId = '4';
$orderLines = array(
    array(
        'description' => 'description 1',
        'itemId'      => 'id 01',
        'quantity'    => -1,
        'unitPrice'   => 1.1,
        'goodsType'   => 'item',
    ),
    array(
        'description' => 'new item',
        'itemId'      => 'new id',
        'quantity'    => 1,
        'unitPrice'   => 1.1,
        'goodsType'   => 'item',
    ),
);
$response = $api->captureReservation($paymentId);
if (!$response->wasSuccessful()) {
    throw new Exception($response->getErrorMessage());
}

$response = $api->updateOrder($paymentId, $orderLines);

if (!$response->wasSuccessful()) {
    throw new Exception($response->getErrorMessage());
}
