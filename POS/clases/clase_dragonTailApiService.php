<?php

class DragonTailApiService {
    
    static $endpointsRoutes = array(
        "createOrder" => array(
            "method" => "POST"
        ),
        "getOrder"    => array(
            "method" => "GET"
        ),
        "createRider" => array(
            "method" => "POST"
        ),
        "cleanData"   => array(
            "method" => "POST"
        ),
        "login"       => array(
            "method" => "POST"
        ),
    );

    static function getEndPoint($actionName) {
        $endpointData = self::getEnpointData($actionName);
        $baseUrl = self::getBaseUrl();
        $methodToReturn = "POST";
        return array(
            "method" => $methodToReturn,
            "url"    => $baseUrl
        );
    }

    static function getEnpointData($actionName) {
        return self::$endpointsRoutes[$actionName];
    }

    static function getsubRoutes($actionName) {
        $dargonTailConfig = new DragonTailConfig();
        return $dargonTailConfig->getsubRoutes($actionName);
    }

    static function getBaseUrl() {
        $dargonTailConfig = new DragonTailConfig();
        return $dargonTailConfig->getBaseUrl();
    }

    static function bulidUrl($mainRoute, $subRoutes) {
        return $mainRoute.$subRoutes;
    }
}