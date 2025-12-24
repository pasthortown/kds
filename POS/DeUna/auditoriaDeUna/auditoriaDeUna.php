<?php 

class auditoriaDeUna {

    public function guardarAuditoriaDeUna($datosTransaccion, $respuestaDeuna, $accion, $idCabeceraOrdenPedidoQPM, $rst_id, $urlDeuna, $idUserPos)
    {

        $datosAuditoria['IDAuditoriaTransaccion'] = $idCabeceraOrdenPedidoQPM;
        $datosAuditoria['rst_id'] = $rst_id;
        $datosAuditoria['atran_modulo'] = 'FACTURACION';
        $datosAuditoria['atran_descripcion'] = 'TX DEUNA: ' . $idCabeceraOrdenPedidoQPM. ' url:' .$urlDeuna;
        $datosAuditoria['atran_accion'] = $accion;
        $datosAuditoria['Auditoria_TransaccionVarchar1'] = json_encode($datosTransaccion);
        $datosAuditoria['Auditoria_TransaccionVarchar2'] = json_encode($respuestaDeuna);
        $datosAuditoria["IDUsersPos"]= $idUserPos;
        include_once "./modelos/TransaccionesDeUna.php";
        $datosDeUna = new TransaccionesDeUna;
        $datosDeUna->ingresarAuditoriaDeUna($datosAuditoria);
    }

}

?>