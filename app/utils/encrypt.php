<?php

class Encrytp
{
    private $ciphering = "AES-128-CTR";
    // private $iv_length = openssl_cipher_iv_length($this->ciphering);
    private $options = 0;
    private $encryption_iv = '1234567891011121';
    private $encryption_key = "PoSaDmIn";

    public function encrypt_string($str)
    {
        $encryption = openssl_encrypt($str, $this->ciphering, $this->encryption_key, $this->options, $this->encryption_iv);
        return $encryption;
    }

    public function decrypt_string($estr)
    {
        $decryption = openssl_decrypt($estr, $this->ciphering, $this->encryption_key, $this->options, $this->encryption_iv);
        return $decryption;
    }
}