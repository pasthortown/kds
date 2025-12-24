<?php
class ServicioTarjeta extends sql {
    function __construct() {
        parent::__construct();
    }

    public function consutlarPolitica($cadena) {
        $lc_sql = "EXEC [config].[servicio_tarjeta_consultar_politica] '$cadena'";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs = array(
                    'usuario'               => trim(mb_convert_encoding($row['usuario'], 'UTF-8', 'ISO-8859-1')),
                    'contrasena'            => trim(mb_convert_encoding($row['contrasena'], 'UTF-8', 'ISO-8859-1')),
                    'endpointLogin'         => trim(mb_convert_encoding($row['endpoint_login'], 'UTF-8', 'ISO-8859-1')),
                    'endpointOrquestador'   => trim(mb_convert_encoding($row['endpoint_orquestador'], 'UTF-8', 'ISO-8859-1')),
                    'timeout'               => trim(mb_convert_encoding($row['timeout'], 'UTF-8', 'ISO-8859-1')),
                    'aplica'                => trim(mb_convert_encoding($row['aplica'], 'UTF-8', 'ISO-8859-1')),
                    'puerto'                => trim(mb_convert_encoding($row['puerto'], 'UTF-8', 'ISO-8859-1')),
                    'protocolo'             => trim(mb_convert_encoding($row['protocolo'], 'UTF-8', 'ISO-8859-1')),
                );        
            }
            return $this->lc_regs; 
        }
    }

    public function servicioTarjetaOrquestador($tipo, $dispositivo, $factura, $valor, $valorPropina, $formaPago, $codigoRestaurante, $idEstacion, $idUsuario) {
        $lc_sql = "EXEC config.servicio_tarjeta_orquestador '$dispositivo', '$tipo', '$factura', $valor, $valorPropina, '$formaPago', $codigoRestaurante, '$idEstacion', '$idUsuario';";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs = array(
                    'tipoMensaje'           => trim(mb_convert_encoding($row['tipo_mensaje'], 'UTF-8', 'ISO-8859-1')),
                    'tipoTransaccion'       => trim(mb_convert_encoding($row['tipo_transaccion'], 'UTF-8', 'ISO-8859-1')),
                    'codigoAdquiriente'     => trim(mb_convert_encoding($row['codigo_adquiriente'], 'UTF-8', 'ISO-8859-1')),
                    'codigoDiferido'        => trim(mb_convert_encoding($row['codigo_diferido'], 'UTF-8', 'ISO-8859-1')),
                    'plazoDiferido'         => trim(mb_convert_encoding($row['plazo_diferido'], 'UTF-8', 'ISO-8859-1')),
                    'mesGracia'             => trim(mb_convert_encoding($row['mes_gracia'], 'UTF-8', 'ISO-8859-1')),
                    'montoTotal'            => trim(mb_convert_encoding($row['monto_total'], 'UTF-8', 'ISO-8859-1')),
                    'montoBaseIva'          => trim(mb_convert_encoding($row['monto_base_iva'], 'UTF-8', 'ISO-8859-1')),
                    'montoBaseSinIva'       => trim(mb_convert_encoding($row['monto_base_sin_iva'], 'UTF-8', 'ISO-8859-1')),
                    'montoIva'              => trim(mb_convert_encoding($row['monto_iva'], 'UTF-8', 'ISO-8859-1')),
                    'impuestoServicio'      => trim(mb_convert_encoding($row['impuesto_servicio'], 'UTF-8', 'ISO-8859-1')),
                    'propinaServicio'       => trim(mb_convert_encoding($row['propina_servicio'], 'UTF-8', 'ISO-8859-1')),
                    'montoFijo'             => trim(mb_convert_encoding($row['monto_fijo'], 'UTF-8', 'ISO-8859-1')),
                    'secuencial'            => trim(mb_convert_encoding($row['secuencial'], 'UTF-8', 'ISO-8859-1')),
                    'hora'                  => trim(mb_convert_encoding($row['hora'], 'UTF-8', 'ISO-8859-1')),
                    'fecha'                 => trim(mb_convert_encoding($row['fecha'], 'UTF-8', 'ISO-8859-1')),
                    'numeroAutorizacion'    => trim(mb_convert_encoding($row['numero_autorizacion'], 'UTF-8', 'ISO-8859-1')),
                    'mid'                   => trim(mb_convert_encoding($row['mid'], 'UTF-8', 'ISO-8859-1')),
                    'tid'                   => trim(mb_convert_encoding($row['tid'], 'UTF-8', 'ISO-8859-1')),
                    'cid'                   => trim(mb_convert_encoding($row['cid'], 'UTF-8', 'ISO-8859-1')),
                    'rqaut_id'              => trim(mb_convert_encoding($row['rqaut_id'], 'UTF-8', 'ISO-8859-1')),
                );
            }

            return $this->lc_regs; 
        }
    }

    public function servicioTarjetaOrquestadorResponse($factura, $json, $rqaut_id) {
        $lc_sql = "EXEC config.servicio_tarjeta_orquestador_response '$factura', '$json', $rqaut_id;";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs = array(
                    'IDGenerado'           => trim(mb_convert_encoding($row['IDGenerado'], 'UTF-8', 'ISO-8859-1')),
                );
            }

            return $this->lc_regs; 
        }
    }
}
