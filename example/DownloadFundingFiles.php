<?php
require_once __DIR__.'/base.php';
$api = InitializeAltapayMerchantAPI();

// For the purpose of this example will be assumed that there is one page
$pageCount = 1;
for ($page = 1; $page <= $pageCount; $page++) {
    $response = $api->getFundingList($page);
    if (!$response->wasSuccessful()) {
        throw new Exception('The funding list could not be fetched: '.$response->getErrorMessage());
    }
    foreach ($response->getFundings() as $funding) {
        echo 'There is a funding of '.$funding->getAmount().' '.$funding->getCurrency().', made on '.$funding->getFundingDate().PHP_EOL;
        $csv = $api->downloadFundingCSV($funding);
        if (!$csv) {
            //throw new Exception('The funding CSV file '. $funding->getFilename() .' could not be found');
            //or
            echo 'The funding CSV file '.$funding->getFilename().' could not be found.'.PHP_EOL;
        } else {
            echo 'The content of the funding CSV file('.$funding->getFilename().') is:'.PHP_EOL.$csv.PHP_EOL;
        }
    }
}
