<?php

use M2U\Helpers\M2UCallback;
use M2U\Helpers\Ajax;

header('Content-Type: application/json; charset=utf-8', true, 200);

$visitor_ip = $_SERVER["REMOTE_ADDR"];
if (Ajax::cidr_match($visitor_ip)) {
    $visitor_ip = $_SERVER["HTTP_CF_CONNECTING_IP"];
}

$maybank_callback = false;
$maybank_ip = array('202.162.18.247', '202.162.17.179', '203.153.92.109');

/*
 * This should be not used.
 */
if (defined('DEVELOPMENT_MODE') && DEVELOPMENT_MODE) {
    $route[1] = CALLBACK_ID;
}

if (filter_var($visitor_ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
    $ignore_ip = true;
} else {
    $ignore_ip = false;
}

foreach ($maybank_ip as $ip) {
    if ($ip === $visitor_ip || $ignore_ip && isset($route[1])) {
        if ($route[1] === CALLBACK_ID) {
            /*
             * Send Callback to Callback URL
             * Display JSON Output required by the Maybank2u Pay
             */
            $m2u = new M2UCallback;
            echo $m2u->send_callback()->get_output();
            exit;
        }
    }
}


/*
 * If not Maybank, then do the API Action
 */
if (isset($_POST['do']) && isset($_POST['urlid']) && isset($_POST['validation'])) {
    $ajax = new Ajax($_POST['do'], $_POST['urlid'], $_POST['validation']);
    echo $ajax->get_output();
}