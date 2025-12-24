<?php
class ProveedorNoAsignado extends sql
{
  
    public function __construct()
    {
        parent::__construct();
    }

    function actualizarPedido($idFactura, $idApp, $medio,$idRestaurante)
    {
        try {
            
            $trama = [];
            $trama['idFactura'] = $idFactura;
            $trama['idApp'] = $idApp;
            $trama['medio'] = $medio;
            $trama['mensaje'] = "Proveedor NINGUNO asignado al cambio de estado automatico";
            $tramaJson = json_encode($trama);

            $this->updateCabeceraApp('1',$idRestaurante,$idApp, $tramaJson);

            $this->guardarAuditoriaEstadosApp('1', 'No request', 'Crear Orden: ' . $idFactura . ' ' . $medio, 200, $tramaJson);
            return json_encode(["Pedido actualizado correctamente"]);
        } catch (Exception $e) {
            $this->guardarAuditoriaEstadosApp('1', 'No request - Error', 'Completar orden: ' . $idFactura, 'ERROR', json_encode($e));
            return $e;
        }
    }

    function updateCabeceraApp($opcion,$idRestaurante,$codigoApp, $trama)
    {
        $lc_sql = "EXEC [facturacion].[IAE_CabeceraApp] '$opcion',$idRestaurante,'$codigoApp',@codigo_externo='$codigoApp', @respuestaBringg='$trama'";
        $result = $this->fn_ejecutarquery($lc_sql);
        if ($result){ return true; }else{ return false; };
    }

    function guardarAuditoriaEstadosApp($opcion, $url, $peticion, $estado, $mensaje)
    {
        $lc_sql = "EXEC [config].[IAE_Auditoria_Estados_App] '$opcion','$url', '$peticion', '$estado', '$mensaje'";

        try {
            $this->fn_ejecutarquery($lc_sql);
            return true;
        } catch (Exception $e) {
            return $e;
        }
        return false;
    }
}
