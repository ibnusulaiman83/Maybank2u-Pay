<?php

use M2U\Helpers\Create;

/*
 * Filter non character and number
 * https://stackoverflow.com/questions/5199133/function-to-return-only-alpha-numeric-characters-from-string
 */

$order_info = isset($_REQUEST['info']) ? $_REQUEST['info'] : ''; // Any valid string
$amount = isset($_REQUEST['amount']) ? $_REQUEST['amount'] : '0'; // 300 for RM300.00
$name = isset($_REQUEST['name']) ? $_REQUEST['name'] : NULL; // Buyer Name
$email = isset($_REQUEST['email']) ? $_REQUEST['email'] : NULL; // Buyer Email
$phone = isset($_REQUEST['phone']) ? $_REQUEST['phone'] : NULL; // Buyer Phone
$redirect_url = isset($_REQUEST['redirect']) ? $_REQUEST['redirect'] : NULL;
$callback_url = isset($_REQUEST['callback']) ? $_REQUEST['callback'] : NULL;
$output_type = isset($_REQUEST['output_type']) ? $_REQUEST['output_type'] : NULL;
$validation = isset($_REQUEST['validation']) ? $_REQUEST['validation'] : '';

/*
 * Validate the REQUEST Data & Instantiate an Object
 * "acctid" is a reference that sent to the Maybank to identify the transaction
 */
$create = new Create([$order_info, $amount, $name, $email, $phone, $redirect_url, $callback_url, $output_type], $validation);

/*
 * Format the amount properly
 */
$amount = number_format(floatval($amount), 2);

if ($create->status) {
    $create->create_bill();
    $array = array(
        'url' => $create->get_url_id(),
        'id' => $create->get_id()
    );
} else {
    $array = array(
        'reason' => 'Validation Failed'
    );
}

if ($output_type === 'json' || !$create->status) {
    header('Content-Type: application/json; charset=utf-8', true, 200);
    echo json_encode($array, true);
} else {
    header('Location: ' . $array['url']);
}