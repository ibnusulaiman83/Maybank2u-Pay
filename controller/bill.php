<?php

use M2U\Helpers\Pay;
use M2U\M2UPay;

$urlid = $route[1];

$pay = new Pay($urlid);
$bill = $pay->get_bill();

if (!$bill){
    exit('Invalid Request');
}

$m2u_json = array(
    'amount' => $bill['Amt'],
    'accountNumber' => $bill['AcctId'],
    'payeeCode' => PAYEE_CODE
);

if (MODE === 'UAT'){
    $envType = 1;
}else if (MODE === 'PRODUCTION'){
    $envType = 2;
}else {
    $envType = 0;
}

$M2UPay = new M2UPay();
$encrypt_json = $M2UPay->getEncryptionString($m2u_json, $envType);
$data = json_decode($encrypt_json, true);

/*
 * Check if Bills expired
 */
$bill_expired = false;
$previous_time = $bill['Timestamp'] + 60 * BILL_EXPIRY;
$current_time = time();
if ($previous_time < $current_time){
    $bill_expired = true;
}

/*
 * Compute Validation Hash for Ajax Call
 */

$validation_hash = hash_hmac('sha256', $bill['URLId'], SIGNATURE);

/*
 *  Load View
 */
require __DIR__ . '/../views/bill.php';