<?php
namespace M2U\Helpers;

class M2UCallback
{

    public $array;
    private $AcctId;
    private $dataMsg;

    public function __construct()
    {
        global $db;
        $postdata = file_get_contents('php://input');

        $data = json_decode($postdata, true);

        $dataMsg = $data['Msg'];
        $this->dataMsg = $dataMsg;
        $bill = $db->get_urlid($dataMsg['AcctId']);

        $this->dataMsg['OrderInfo'] = $bill['OrderInfo'];
        $this->dataMsg['payment_status'] = $dataMsg['StatusCode'] === '00' ? 'true' : 'false';
        $this->dataMsg['validation'] = $this->get_validation($this->dataMsg);
        $this->AcctId = $dataMsg['AcctId'];
        $refId = $dataMsg['RefId'];
        $pmtType = $dataMsg['PmtType'];
        $statusCode = '0';

        $this->array = [
            'Msg' =>
            [
                'PmtType' => $pmtType,
                'RefId' => $refId,
                'StatusCode' => $statusCode
            ]
        ];

        $this->update($dataMsg);
    }

    private function get_validation($dataMsg)
    {
        $str = '';
        foreach ($dataMsg as $key => $value) {
            $str .= $value;
        }
        return hash_hmac('sha256', $str, SIGNATURE);
    }

    private function update($data = array())
    {
        global $db;
        $db->update_order($data);
    }

    public function send_callback()
    {
        global $db;
        $bill = $db->get_urlid($this->AcctId);
        $process = curl_init();
        curl_setopt($process, CURLOPT_URL, $bill['CallbackUrl']);
        curl_setopt($process, CURLOPT_HEADER, 0);
        curl_setopt($process, CURLOPT_TIMEOUT, 10);
        curl_setopt($process, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($process, CURLOPT_POSTFIELDS, $this->dataMsg);
        $return = curl_exec($process);
        curl_close($process);
        if (strtoupper($return) !== 'OK') {
            error_log('Callback Failed: ' . print_r($this->dataMsg, true) . ' ' . $return);
        }
        return $this;
    }

    public function get_output()
    {
        return json_encode($this->array, true);
    }
}
