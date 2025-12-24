<?php

require_once "../../../../exceptions/GeneralException.php";
require_once "../../../../system/conexion/clase_sql.php";
require_once "../../../../clases/clase_dragontailApiService.php";
require_once "../../../../clases/clase_dragonTailOrders.php";
require_once "../../../../clases/clase_dragontailOrderStatus.php";
require_once "../../../../clases/clase_dragonTailConfig.php";
function changeStatus($codApp, $medio, $estado){
    DragontailOrderStatus::checkMedio($medio);
    DragontailOrderStatus::changeStatus($codApp, $estado);
    $mxpStatus = DragontailOrderStatus::getStatusFromEstado($estado);
    return "estado del pedido ha sido cambiado a " . $mxpStatus;
}
