<?php
class ServicioReImpresion extends sql {
    function __construct() {
        parent::__construct();
    }

    public function impresionFactura($transaccion, $IDestacion, $impresora) {
        $lc_sql = "EXEC [impresion].[reimpresion_facturacion] '$transaccion', '$IDestacion', '$impresora'";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array(
                    "numeroImpresiones" => $row["numeroImpresiones"],
                    "tipo"              => $row["tipo"],
                    "impresora"         => utf8_encode($row["impresora"]),
                    "formatoXML"        => utf8_encode($row["formatoXML"]),
                    "jsonData"          => utf8_encode($row["jsonData"]),
                    "jsonRegistros"     => utf8_encode($row["jsonRegistros"])
                );
            }
            
            return ($this->lc_regs); 
        }
    }

    public function impresionNotaCredito($idRestaurante, $idEstacion, $transaccion, $impresora) {
        $lc_sql = "EXEC [impresion].[reimpresion_nota_credito] $idRestaurante, '$transaccion', '$idEstacion', '$impresora'";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array(
                    "numeroImpresiones" => $row["numeroImpresiones"],
                    "tipo" => $row["tipo"],
                    "impresora" => utf8_encode($row["impresora"]),
                    "formatoXML" => utf8_encode($row["formatoXML"]),
                    "jsonData" => utf8_encode($row["jsonData"]),
                    "jsonRegistros" => utf8_encode($row["jsonRegistros"])
                );
            }
            
            return ($this->lc_regs); 
        }
    }

    public function impresionOrdenPedido($transaccion, $IDestacion, $impresora) {
        $lc_sql = "EXEC [impresion].[reimpresion_orden_pedido] '$transaccion', '$IDestacion', '$impresora'";
        $this->lc_regs = array();
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $row["jsonRegistros"] = preg_replace("/[\r\n|\n|\r]+/", PHP_EOL, $row["jsonRegistros"]);
                $row["jsonData"] = preg_replace("/[\r\n|\n|\r]+/", PHP_EOL, $row["jsonData"]);
                
                $this->lc_regs[] = array(
                    "numeroImpresiones" => $row["numeroImpresiones"],
                    "tipo" => $row["tipo"],
                    "impresora" => utf8_encode($row["impresora"]),
                    "formatoXML" => utf8_encode($row["formatoXML"]),
                    "jsonData" => utf8_encode($row["jsonData"]),
                    'jsonRegistros' => utf8_encode($row["jsonRegistros"])
                );
            }

            return ($this->lc_regs); 
        }
    }

    public function descripcionTipoImpresora( $imp_nombre ) {
        $consulta = "SELECT [impresion].[fnServicioApiImpresionObtenerDescripcionTipoImpresora]('$imp_nombre')";
        $descripcionTipoImpresora = '';

        try {
            if ( $this->fn_ejecutarquery( $consulta ) ) {
                $registro = array();
                $registro = $this->fn_leerarreglo();
                $descripcionTipoImpresora = $registro[0];
            }
        } catch (Exception $e) { ; }

        return $descripcionTipoImpresora;
    }

    public function obtenerIpImpresora($nombreImpresora){
        $this->lc_regs = [];
        $query = "SELECT [impresion].[fn_ColeccionRestaurante_ServicioApiImpresionUrl]('$nombreImpresora')";
        try {
            $this->fn_ejecutarquery( $query );
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs['urlApiImpresion'] = $row[0];
         }
            $this->lc_regs['registros'] = $this->fn_numregistro();  
        } catch (Exception $e) {
            return $e;
        }
        return $this->lc_regs;
    }

    public function auditoriaApiImpresion($tipoDocumento, $estado, $impresora, $idEstacion, $idUsuario, $url, $payload, $response, $datosAdicionales = null, $codigo_app = null, $codigo_factura = null, $tipoEntrega = null, $reimpresion = 1) {
        $sqlSetence = $tipoEntrega != 'delivery' ? 'INSERT' : 'UPDATE';
        $response = $estado == 'ERROR' ? str_replace("'", '"', json_encode($response)) : $response;
        $lc_sql = "EXEC [impresion].[IAE_AuditoriaApiImpresion] '$tipoDocumento', '$estado', '$impresora', '$idEstacion', '$idUsuario', '$url', '".utf8_decode($payload)."', '$response', '$datosAdicionales', '$sqlSetence', '$codigo_app', '$codigo_factura', '$reimpresion'";
        return $this->fn_ejecutarquery($lc_sql);
    }
}
