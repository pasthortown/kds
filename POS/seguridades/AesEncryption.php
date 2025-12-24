<?php

class AESEncriptar
{


    function encriptarDatos($data, $key)
    {
        $encrypt_method = "AES-256-CBC";
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($encrypt_method));
        $encrypted = openssl_encrypt($data, $encrypt_method, hex2bin($key), 0, hex2bin($iv));
        return ($encrypted);
    }

    function DesencriptarDatos($data, $key)
    {
        $encrypt_method = "AES-256-CBC";
        $iv = substr($data, 0, 32);
        $dataR = substr($data, 32);
        return openssl_decrypt(base64_encode(hex2bin($dataR)), $encrypt_method, hex2bin($key), 0, hex2bin($iv));
    }


}


