<?php

class pagoTarjetas extends sql 
{
    function __construct() 
    {        
        parent::__construct();
    }
    public function insertarRequerimientoTramaDinamica($lc_datos) 
        {
                $lc_sql = "EXEC [facturacion].[IAE_SWT_InsertaTramaDeRequerimientoDinamica]  '$lc_datos[6]','$lc_datos[1]','$lc_datos[8]','$lc_datos[7]','$lc_datos[9]','$lc_datos[2]','$lc_datos[12]','$lc_datos[10]','$lc_datos[11]','$lc_datos[4]','$lc_datos[3]','$lc_datos[5]'";
                if ($this->fn_ejecutarquery($lc_sql)) 
                    {
                        while ($row = $this->fn_leerarreglo()) 
                        {
                                $this->lc_regs['str'] = 1;
                                $this->lc_regs['mensaje'] = $row["mensaje"];
                                $this->lc_regs['estado'] = $this->ifNum($row["estado"]);
                        }
                        $this->lc_regs['str'] = $this->fn_numregistro();
                    }

                    try {
                        @error_log( 
                            date('d-m-Y H:i:s')
                            ." - Documento: js/ajax_pagoTarjetaDinamico.js"
                            ." - Consulta: insertarRequerimientoTramaDinamica"
                            ." - Sentencia: $lc_sql"
                            ." - Salida:"
                            ." mensaje; ".$this->lc_regs['mensaje']
                            ." , estado; ".$this->lc_regs['estado']
                            ."\n"
                        , 3, "../logs/info.log" );
                    } catch (Exception $e) { ; }

                    return json_encode($this->lc_regs);

        }
        public function fn_esperaRequerimientoTramaDinamica($lc_datos) 
        {
            $lc_sql = "exec [facturacion].[USP_esperaRespuestaRequerimientoAutorizacion] '$lc_datos[0]'";

            if ($this->fn_ejecutarquery($lc_sql)) 
                    {
                        while ($row = $this->fn_leerarreglo()) 
                        {
                            $this->lc_regs['existe'] =$this->ifNum($row["existe"]) ;
                            if ($row["existe"] == 1) 
                            {
                                $this->lc_regs['rsaut_respuesta'] =utf8_encode( $row["rsaut_respuesta"]);
                                $this->lc_regs['cres_codigo'] = $row["cres_codigo"];
                                $this->lc_regs['fpf_id'] = $row["fpf_id"];
                                $this->lc_regs['rsaut_id'] = $row["rsaut_id"];
                                $this->lc_regs['errorTrama'] = utf8_encode($row["errorTrama"]);
                                $this->lc_regs['codigoAutorizador'] = $row["codigoAutorizador"];
                                $this->lc_regs['respuestaAutorizacion'] = $row["respuestaAutorizacion"];
                            }   
                        }
                        $this->lc_regs['str'] = $this->fn_numregistro();
                    }

                    try {
                        @error_log( 
                            date('d-m-Y H:i:s')
                            ." - Documento: js/ajax_pagoTarjetaDinamico.js"
                            ." - Consulta: esperaRespuestaRequerimientoAutorizacion"
                            ." - Sentencia: $lc_sql"
                            ." - Salida:"
                            ." existe; ".$this->lc_regs['existe']
                            ." , rsaut_respuesta; ".( isset($this->lc_regs['rsaut_respuesta']) ? $this->lc_regs['rsaut_respuesta'] : '' )
                            ." , cres_codigo; ".( isset($this->lc_regs['cres_codigo']) ? $this->lc_regs['cres_codigo'] : '' )
                            ." , fpf_id; ".( isset($this->lc_regs['fpf_id']) ? $this->lc_regs['fpf_id'] : '' )
                            ." , rsaut_id; ".( isset($this->lc_regs['rsaut_id']) ? $this->lc_regs['rsaut_id'] : '' )
                            ." , errorTrama; ".( isset($this->lc_regs['errorTrama']) ? $this->lc_regs['errorTrama'] : '' )
                            ." , codigoAutorizador; ".( isset($this->lc_regs['codigoAutorizador']) ? $this->lc_regs['codigoAutorizador'] : '' )
                            ." , respuestaAutorizacion; ".( isset($this->lc_regs['respuestaAutorizacion']) ? $this->lc_regs['respuestaAutorizacion'] : '' )
                            ."\n"
                        , 3, "../logs/info.log" );
                    } catch (Exception $e) { ; }

                    return json_encode($this->lc_regs);
        }

        public function fn_validarSinTarjeta( $lc_datos ){
            $lc_sql = "EXEC [facturacion].[USP_ValidacionSinTarjeta] '$lc_datos[0]'";
            if ($this->fn_ejecutarquery($lc_sql)) {
                while($row = $this->fn_leerarreglo()) {

                
                    $this->lc_regs['existe'] = $row["existe"];                           
                    if ($row["existe"] == 1) 
                    {
                        $this->lc_regs['rsaut_respuesta'] =utf8_encode( $row["rsaut_respuesta"]);
                        $this->lc_regs['cres_codigo'] = $row["cres_codigo"];
                        $this->lc_regs['fpf_id'] = $row["fpf_id"];
                        $this->lc_regs['rsaut_id'] = $row["rsaut_id"];
                        $this->lc_regs['errorTrama'] = utf8_encode($row["errorTrama"]);
                        $this->lc_regs['codigoAutorizador'] = $row["codigoAutorizador"];
                        $this->lc_regs['respuestaAutorizacion'] = $row["respuestaAutorizacion"];
                    }else{
                        $this->lc_regs['existe'] = $row["existe"];
                        $this->lc_regs['mensaje'] = $row["mensaje"];

                    }

                }

            }
            $this->lc_regs['str'] = $this->fn_numregistro();
            
            return json_encode($this->lc_regs);

        }
        
        
        public function fn_consultaSecuenciaTimeOut($lc_datos) 
        {
            $lc_sql = "exec [facturacion].[USP_valida_secuencia_timeOut] $lc_datos[1],$lc_datos[0]";
                if ($this->fn_ejecutarquery($lc_sql)) 
                    {
                        while ($row = $this->fn_leerarreglo()) 
                        {
                                $this->lc_regs['secuenciaConfigurada'] = $row["secuenciaConfigurada"];
                        }
                        $this->lc_regs['str'] = $this->fn_numregistro();
                    }

                    try {
                        @error_log( 
                            date('d-m-Y H:i:s')
                            ." - Documento: js/ajax_pagoTarjetaDinamico.js"
                            ." - Consulta: consultaSecuenciaTimeOuts"
                            ." - Factura: ".$lc_datos[3]
                            ." - Nivel: ".$lc_datos[2]
                            ." - Sentencia: $lc_sql"
                            ." - Salida:"
                            ." secuenciaConfigurada; ".$this->lc_regs['secuenciaConfigurada']
                            ."\n"
                        , 3, "../logs/info.log" );
                    } catch (Exception $e) { ; }

                    return json_encode($this->lc_regs);
        }
        
        public function fn_actualizaEstadoRequerimiento($lc_datos) {
            $lc_sql = "exec [facturacion].[USP_actualizaEstadoRequerimiento] '$lc_datos[0]','$lc_datos[1]','$lc_datos[2]'";
            if ($this->fn_ejecutarquery($lc_sql)) {
                while ($row = $this->fn_leerarreglo()) 
                { 
                    $this->lc_regs['Respuesta'] = $row["Respuesta"];
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
            }
            return json_encode($this->lc_regs);
        }
    
        public function fn_consultaDiferenciaTiempo($lc_datos) {
            $lc_sql = "exec [facturacion].[USP_Diferencia_tiempo] '$lc_datos[0]', '$lc_datos[1]'";
            if ($this->fn_ejecutarquery($lc_sql)) {
                while ($row = $this->fn_leerarreglo()) 
                { 
                    $this->lc_regs['DiferenciaTiempo'] = $row["DiferenciaTiempo"];
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
            }
            return json_encode($this->lc_regs);
        }
}