<?php
class DragontailOrderStatus {

    static function validate($codApp) {
        DragonTailConfig::validStatusEntregado($codApp);
        DragonTailConfig::validateOrderIsBilled($codApp);
    }

    static function checkMedio($medio) {
        $agregadores = DragonTailConfig::getMediosAgregador();
        foreach($agregadores as $agregador) {
            if (!strcasecmp($agregador['agregadores'], $medio)) {
                return $agregador;
            }
        }
        throw new GeneralException("este medio no es agregador");
    }

    static function changeStatus($codApp, $estado) {
        $status = self::getStatusFromEstado($estado) ;
        DragonTailConfig::changeOrderStatus($codApp, $status);
        return $status;
    }

    static function getStatusFromEstado($estado) {
        if ($estado == 'success') {
            $result = 'ENTREGADO';
        } elseif ($estado == 'error') {
            $result = 'POR ASIGNAR';
        }
        return $result;
    }
}
