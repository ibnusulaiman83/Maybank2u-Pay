<?php
namespace M2U\Helpers;

use M2U\Helpers\Pay;

class Ajax
{

    private $urlid;
    private $validation;
    private $do;
    private $array;

    public function __construct($do, $urlid, $validation)
    {
        $this->do = $do;
        $this->urlid = preg_replace("/[^a-zA-Z0-9]+/", "", $urlid);
        $this->validation = $validation;

        if ($this->do === 'check-status') {
            $this->check_status();
        }
    }

    private function check_status()
    {
        $validation_hash = hash_hmac('sha256', $this->urlid, SIGNATURE);
        /*
         * Validate Request. In case anyone trying to query bill id 
         * without owner concern, it will get rejected
         */
        if ($this->validation !== $validation_hash) {
            exit('Validation Failed');
        }

        $pay = new Pay($this->urlid);
        $bill = $pay->get_bill();

        if ($bill['StatusCode'] === '00') {
            $amount = number_format($bill['Amt'] / 100);
            /*
             * Provide Validation. Enable the receiving system to validate
             */
            $str = $bill['OrderInfo'] . 'true' . $amount . $bill['RefId'] . $bill['TrnDateTime'];
            $new_validation = hash_hmac('sha256', $str, SIGNATURE);
            $this->array = array(
                'order_info' => $bill['OrderInfo'],
                'payment_status' => 'true',
                'payment_amount' => $amount,
                'maybank_refid' => $bill['RefId'],
                'maybank_trndatetime' => $bill['TrnDateTime'],
                'hash_validation' => $new_validation,
            );
        } else {
            $str = $bill['OrderInfo'] . 'false';
            $new_validation = hash_hmac('sha256', $str, SIGNATURE);
            $this->array = array(
                'order_info' => $bill['OrderInfo'],
                'payment_status' => 'false',
                'hash_validation' => $new_validation
            );
        }
    }

    public function get_output()
    {
        return json_encode($this->array, true);
    }

    public static function cidr_match($ip)
    {
        foreach (file('https://www.cloudflare.com/ips-v4') as $cidr) {
            list($subnet, $mask) = explode('/', $cidr);
            if ((ip2long($ip) & ~((1 << (32 - $mask)) - 1) ) == ip2long($subnet)) {
                return true;
            }
            continue;
        }
        return false;
    }
}
