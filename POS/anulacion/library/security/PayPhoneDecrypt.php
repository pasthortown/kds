<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class PayPhoneDecrypt{
    private $privateKey;

    public function __construct($privateKey) {
        $this->privateKey = $privateKey;
    }

    public function Execute($model) {
        include_once ('/../core/Crypt/RSA.php');
        include_once ('/../core/Crypt/TripleDES.php');

        //Desencriptar con RSA
        $rsa = new Crypt_RSA();

        $rsa->loadKey($this->privateKey, CRYPT_RSA_PRIVATE_FORMAT_XML); // private key
        $rsa->setEncryptionMode(CRYPT_RSA_ENCRYPTION_PKCS1);

        $SessionKey = $rsa->decrypt(base64_decode($model->SessionKey));
        $IV = $rsa->decrypt(base64_decode($model->IV));

        //Desencriptar con tripleDES
        $cipher = new Crypt_TripleDES();
        $cipher->setKey($SessionKey);
        $cipher->setIV($IV);
        
        $XmlReq = $cipher->decrypt(base64_decode($model->XmlReq));

        $result = json_decode(utf8_decode($XmlReq));
        return $result;
    }
}
