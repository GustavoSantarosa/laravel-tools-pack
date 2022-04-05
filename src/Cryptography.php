<?php

namespace GustavoSantarosa\LaravelToolPack;


class Cryptography
{
    private $secretKey;
    private $algorithm;

    public function __construct($secretKey, $algorithm = 'sha256')
    {
        $this->secretKey = $secretKey;
        $this->algorithm = $algorithm;
    }

    /**
     * decrencryptt the data
     *
     * @param string $data
     *
     * @return string
     */

    public function encrypt($data)
    {
        $ivlen = openssl_cipher_iv_length($cipher = "AES-128-CBC");
        $iv = openssl_random_pseudo_bytes($ivlen);
        $ciphertext_raw = openssl_encrypt($data, $cipher, $this->secretKey, 0, $iv);
        $hmac = hash_hmac($this->algorithm, $ciphertext_raw, $this->secretKey, $as_binary = true);

        return base64_encode($iv . $hmac . $ciphertext_raw);
    }

    /**
     * decrypt the data
     *
     * @param string $data
     *
     * @return mixed (string if success, false if failed)
     */

    public function decrypt($data)
    {
        $c = base64_decode($data);
        $ivlen = openssl_cipher_iv_length($cipher = "AES-128-CBC");
        $iv = substr($c, 0, $ivlen);
        $hmac = substr($c, $ivlen, $sha2len = 32);
        $ciphertext_raw = substr($c, $ivlen + $sha2len);
        $original_plaintext = openssl_decrypt($ciphertext_raw, $cipher, $this->secretKey, 0, $iv);
        $calcmac = hash_hmac($this->algorithm, $ciphertext_raw, $this->secretKey, $as_binary = true);

        if (hash_equals($hmac, $calcmac)) {
            return $original_plaintext;
        }

        return false;
    }
}