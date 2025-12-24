<?php

//////////////////////////////////////////////////////////////
////////DESARROLLADO POR: Isaac Betancourt/////////////////////
////////DESCRIPCION: Clase servicio API apertura///////////////////
///////FECHA CREACION: 25-Octubre-2023//////////////////////////
///////USUARIO QUE MODIFICO: Isaac Betancourt///////////////////
///////DECRIPCION ULTIMO CAMBIO: nuevo api apertura/////////
//////////////////////////////////////////////////////////////

class servicioApiAperturaCajon extends sql {

    function __construct() {
        parent::__construct();
    }
    
    public function impresionAperturaCajon($idCadena, $idFormaPago) {
        $lc_sql = "EXEC [impresion].[IAE_abreCajon] $idCadena, '$idFormaPago'";
        $this->lc_regs = array();
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs['aplicaAperturaCajon'] = trim($row['aplicaAperturaCajon']);
            }
            return ($this->lc_regs); 
        }
    }

    public function auditoriaApiImpresion($tipoDocumento, $estado, $impresora, $idEstacion, $idUsuario, $url, $payload, $response, $datosAdicionales = null, $codigo_app = null, $codigo_factura = null, $tipoEntrega = null, $reimpresion = null, $tca_codigo = null) {

        $sqlSetence = 'INSERT';
        $response = $estado == 'ERROR' ? str_replace("'", '"', json_encode($response)) : $response;

        $lc_sql = "EXEC [impresion].[IAE_AuditoriaApiImpresion] '$tipoDocumento', '$estado', '$impresora', '$idEstacion', '$idUsuario', '$url', '".utf8_decode($payload)."', '$response', '$datosAdicionales', '$sqlSetence', '$codigo_app', '$codigo_factura', 0, $tca_codigo";

        return $this->fn_ejecutarquery($lc_sql);
    }



   

}

?>