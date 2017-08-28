<?php
namespace M2U\Helpers;

class Create
{

    public $status = false;
    private $array;
    private $urlid;

    public function __construct($array, $validation = '')
    {
        if (!empty($validation)) {
            $this->validate_create($array, $validation);
        }
        $this->array = $array;
    }
    /*
     * @return: bool
     */

    public function validate_create($array = array(), $validation = '')
    {
        $str = '';
        foreach ($array as $data) {
            $str .= $data;
        }

        $SHA2 = hash_hmac('sha256', $str, SIGNATURE);

        if ($SHA2 === $validation) {
            $this->status = true;
        } else {
            $this->status = false;
        }
        return $this->status;
    }

    public function create_bill()
    {
        /*
         * If validation fails, do not create bills
         */
        if (!$this->status)
            return false;

        $order_info = $this->array[0];
        $amount = $this->array[1];
        $name = $this->array[2];
        $email = $this->array[3];
        $phone = $this->array[4];
        $redirect_url = $this->array[5];
        $callback_url = $this->array[6];
        $urlid = $this->generateURLID();
        
        /*
         * Store current bill generation time for bills expiration
         */
        $time = time();

        global $db;

        if ($db->set_bill($order_info, $amount, $name, $email, $phone, $urlid, $redirect_url, $callback_url, $time)) {
            $this->urlid = $urlid;
            return $urlid;
        }
        return false;
    }
    
    public function get_id(){
        return $this->urlid;
    }

    public function get_url_id()
    {
        return SYSTEM_URL .'bill/'. $this->urlid;
    }

    public static function generateRandomString()
    {
        $length = BILL_ID_LENGTH;
        $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    private function generateURLID()
    {
        global $db;
        while (true) {
            $urlid = self::generateRandomString();
            if ($db->check_urlid_for_duplicate($urlid)) {
                break;
            }
        }
        return $urlid;
    }
}
