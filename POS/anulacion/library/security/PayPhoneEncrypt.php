<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include_once 'library/models/request/TransactionRequestModel.php';
include_once 'library/models/DataSend.php';
include_once('library/core/Crypt/RSA.php');
include_once('library/core/Crypt/TripleDES.php');
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Encrypt
 *
 * @author egomez
 */
class PayPhoneEncrypt {

    //put your code here
    private $publickey;

    public function __construct($publicKey) {
        $this->publickey = $publicKey;
    }

    public static function GenerateKeys($bits = 2048) {
        $rsa = new Crypt_RSA();
        $rsa->setPublicKeyFormat(CRYPT_RSA_PUBLIC_FORMAT_XML);
        $rsa->setPrivateKeyFormat(CRYPT_RSA_PRIVATE_FORMAT_XML);
        define('CRYPT_RSA_EXPONENT', 65537);
        $keypair = $rsa->createKey($bits);
        return $keypair;
    }

    public function Execute($model, $applicationId, $callbackUrl = null) {
        //Construyo sessionKey de 24bits
        //$sessionKey = (string) rand(111111111111111111111111, 999999999999999999999999); //Número aleatorio
        $sessionKey = (string) rand(1111111111, 4294967296); //Número aleatorio 32 bits = 2 elevado a la 32 potencia
        $sessionKey1 = (string) rand(1111111111, 4294967296);
        $sessionKey = $sessionKey . $sessionKey1;
        
        if (strlen($sessionKey) < 24) {
            $numero_ceros = 24 - strlen($sessionKey);
            for ($i = 0; $i < $numero_ceros; $i++) {
                $sessionKey = "0" . $sessionKey;
            }
        }
        $IV = (string) rand(11111111, 99999999); //número aleatorio

        $cipher = new Crypt_TripleDES();
        $cipher->setKey($sessionKey);
        $cipher->setIV($IV);

        $json_encode = json_encode($model);
        
        $a = $cipher->encrypt($json_encode);
        $XmlReq = base64_encode($a);
       
        $rsa = new Crypt_RSA();

        $rsa->loadKey($this->publickey, CRYPT_RSA_PUBLIC_FORMAT_XML);
        $rsa->setEncryptionMode(CRYPT_RSA_ENCRYPTION_PKCS1);

        //'Encriptando datos...<br/>';
        $sessionKeyRSA = base64_encode($rsa->encrypt($sessionKey));
        $IVRSA = base64_encode($rsa->encrypt($IV));

        $request = new DataSend();
        $request->ApplicationId = $applicationId;
        $request->IV = $IVRSA;
        $request->SessionKey = $sessionKeyRSA;
        $request->XmlReq = $XmlReq;
        $request->CallBackUrl = $callbackUrl;

        return $request;
    }
}