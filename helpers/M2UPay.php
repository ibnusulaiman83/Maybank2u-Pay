<?php
namespace M2U;

class M2UPay
{

    public function getEncryptionString($m2u_json, $envType)
    {


        $amount = array_key_exists('amount', $m2u_json) ? $m2u_json['amount'] : ' ';

        $accountNumber = array_key_exists('accountNumber', $m2u_json) ? $m2u_json['accountNumber'] : ' ';

        $payeeCode = array_key_exists('payeeCode', $m2u_json) ? $m2u_json['payeeCode'] : ' ';

        $refNumber = array_key_exists('refNumber', $m2u_json) ? $m2u_json['refNumber'] : ' ';

        $redirectionurl = array_key_exists('callbackUrl', $m2u_json) ? $m2u_json['callbackUrl'] : ' ';

        //Passed in parameter based on M2U requirment for send string
        $sendString = "";

        if (($accountNumber == null || $accountNumber == "") && ($refNumber != null && $refNumber != ""))
            $sendString = 'Login$' . $payeeCode . '$1$' . $amount . '$1$' . $refNumber . '$$$' . $redirectionurl;
        else if (($accountNumber != null && $accountNumber != "" ) && ($refNumber == null || $refNumber == ""))
            $sendString = 'Login$' . $payeeCode . '$1$' . $amount . '$$$1$' . $accountNumber . '$' . $redirectionurl;
        else
            $sendString = 'Login$' . $payeeCode . '$1$' . $amount . '$1$' . $refNumber . '$1$' . $accountNumber . '$' . $redirectionurl;

        //------M2U Encryption code START-----

        $ITERATIONS = 2;
        $secretKey = pack("C*", 0x00, 0x01, 0x02, 0x03, 0x04, 0x05, 0x06, 0x07, 0x08, 0x09, 0x0a, 0x0b, 0x0c, 0x0d, 0x0e, 0x0f);

        $salt = "Maybank2u simple encryption";

        for ($i = 0; $i < $ITERATIONS; $i++) {
            $sendString = $this->fnEncrypt($salt . $sendString, $secretKey);
        }

        $m_sSendStringEncrypt = urlencode($sendString);
        //------M2U Encryption code END-----
        //m2u endpoint url based on environment type
        switch ($envType) {
            case 1:
                $actionUrl = "https://202.162.18.55:8443/testM2uPayment";
                break;
            case 2:
                $actionUrl = "https://www.maybank2u.com.my/mbb/m2u/m9006_enc/m2uMerchantLogin.do";
                break;
            default:
                $actionUrl = "https://api.discotech.io/v1.0/testM2uPayment";
        }

        //Return the encrypted string and actionUrl as Merchant API response 
        return json_encode(array('encryptedString' => $m_sSendStringEncrypt, 'actionUrl' => $actionUrl));
    }

    //------M2U Encryption Function -----

    public function fnEncrypt($sValue, $sSecretKey)
    {
        $sValue = $this->pkcs5_pad($sValue, mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, 'ecb'));
        return rtrim(
            base64_encode(
                mcrypt_encrypt(
                    MCRYPT_RIJNDAEL_128, $sSecretKey, $sValue, MCRYPT_MODE_ECB, mcrypt_create_iv(
                        mcrypt_get_iv_size(
                            MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB
                        ), MCRYPT_RAND)
                )
            ), "\0"
        );
    }

    //------M2U Encryption Function -----
    public function pkcs5_pad($plainText, $blockSize)
    {
        $pad = $blockSize - (strlen($plainText) % $blockSize);
        return $plainText . str_repeat(chr($pad), $pad);
    }
}
