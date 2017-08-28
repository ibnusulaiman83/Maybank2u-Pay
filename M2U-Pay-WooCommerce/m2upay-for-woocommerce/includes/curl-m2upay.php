<?php

class M2UPay_Curl
{

    private $endpoint;

    public function __construct($endpoint)
    {
        $this->endpoint = $endpoint;
    }

    public function get_url($data)
    {
        $process = curl_init();
        curl_setopt($process, CURLOPT_URL, $this->endpoint . 'create');
        curl_setopt($process, CURLOPT_HEADER, 0);
        curl_setopt($process, CURLOPT_TIMEOUT, 10);
        curl_setopt($process, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($process, CURLOPT_POSTFIELDS, http_build_query($data));
        $return = curl_exec($process);
        curl_close($process);
        //error_log(var_export($return, true));
        return json_decode($return, true);
    }
}
