<?php

/**
 * Description of ExecWebService
 *
 * @author mychael.castro
 */
class ExecWebService {

    function __construct() {
    }

    function executeWs($urlWS) {
        $ch1 = curl_init($urlWS);
        curl_setopt($ch1, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch1, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch1, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: application/json'));
        curl_setopt($ch1, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch1, CURLOPT_CONNECTTIMEOUT, 5);
        $result_price = curl_exec($ch1);
        return $result_price;
    }

}