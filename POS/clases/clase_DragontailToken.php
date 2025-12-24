<?php
class DragonTailToken {

    static function getTokenData() {
        $data = DragonTailConfig::getAllParamVariableV('DRAGONTAIL CONFIGS');
        $data['userLevel'] = (int)$data['userLevel'];
        if (isset($data['ACTIVE'])) {
            unset($data['ACTIVE']);
        }
        return $data;
    }

    static function getTokenFromConfig($storeNo) {
        $collection = 'DRAGONTAIL TOKEN';
        $param = 'TOKEN';
        $config = new DragontailRestaurantConfigs($storeNo);
        return $config->getVariableV($collection,$param);
    }
    
    static function saveToken($token) {
        DragonTailConfig::UpdateToken($token);
    }
}
