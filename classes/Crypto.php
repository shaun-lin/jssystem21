<?php

class Crypto
{
    public $iv;
    public $key;

    public function __construct($key='')
    {
        $this->key = empty($key) ? 'o92e92ensjdcndkjvn' : $key ;
        $this->iv = $this->getIv();
    }

    public function getIV()
    {
        return $this->iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC), MCRYPT_DEV_URANDOM);
    }

    public function encrypt($string)
    {
        $data = $this->iv . mcrypt_encrypt(MCRYPT_RIJNDAEL_128, hash('sha256', $this->key, true), $string, MCRYPT_MODE_CBC, $this->iv);
        return urlencode(base64_encode($data));
    }

    public function decrypt($string)
    {
        if (empty($string)) {
            return "";
        }

        $data = base64_decode(urldecode($string));
        $iv = substr($data, 0, mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC));

        ob_start();
        $decrpted = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_128, hash('sha256', $this->key, true), substr($data, mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC)), MCRYPT_MODE_CBC, $iv), "\0");
        ob_clean();

        return $decrpted;
    }
}