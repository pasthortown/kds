<?php

////////////////////////////////////////////////////////////////////////////////
////////DESARROLLADO POR: CHRISTIAN PINTO///////////////////////////////////////
///////////DESCRIPCION: INICIO DE SESION INGRESO AL SISTEMA CON VALIDACIONES ///
////////////////TABLAS: Control_Estacion, Estacion, Peridodo, User_Pos /////////
////////FECHA CREACION: 12/08/2015//////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

class loguin extends sql {

    //constructor de la clase
    function __construct() {
        //con herencia      
        parent::__construct();
    }

    function fn_consultar($lc_sqlQuery, $lc_datos) {

        switch ($lc_sqlQuery) {
            case 'valida_usuario_logueado':
                $lc_sql = "EXECUTE seguridad.USP_verificausuariologueado '$lc_datos[0]'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs['usr_id'] = isset($row['usr_id'])? $row['usr_id'] : '';
                        $this->lc_regs['usr_usuario'] = isset($row['usr_usuario'])? $row['usr_usuario'] : '';
                        $this->lc_regs['Estacion'] = isset($row['Estacion'])? $row['Estacion'] : '';
                        $this->lc_regs['fondo'] = isset($row['fondo'])? (int) $row['fondo'] : '';
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                }
                return json_encode($this->lc_regs);
                break;

            case 'validaIpConfigurada':
                $lc_sql = "EXECUTE seguridad.USP_validaipperfilperiodo $lc_datos[0],'$lc_datos[1]'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs['existeip'] = isset($row['existeip']) ?  (int) ($row['existeip']) : '';
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                }
                return json_encode($this->lc_regs);
                break;

            case 'validaPeriodoAbierto':
                $lc_sql = "EXECUTE seguridad.USP_validaipperfilperiodo $lc_datos[0],'$lc_datos[1]'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs['periodoabierto'] = isset($row['periodoabierto'])? (int) $row['periodoabierto'] : '';
                        $this->lc_regs['prd_fechaapertura'] = isset($row['prd_fechaapertura'])? $row['prd_fechaapertura'] : '';
                        $this->lc_regs['idperiodo'] = isset($row['idperiodo'])? $row['idperiodo'] : '';
                        $this->lc_regs['idestacion'] = isset($row['idestacion'])? $row['idestacion'] : '';
                        $this->lc_regs['idusuario'] = isset($row['idusuario'])? $row['idusuario']: '';
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                }
                return json_encode($this->lc_regs);
                break;

            case 'traerDatosCadena':
                $lc_query = "EXECUTE seguridad.USP_validaipperfilperiodo $lc_datos[0], '$lc_datos[1]'";
                if ($this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs['cdn_logotipo'] = isset($row['cdn_logotipo'])? $row['cdn_logotipo'] : '';
                        $this->lc_regs['cdn_id'] = isset($row['cdn_id']) ? $row['cdn_id'] : '';
                        $this->lc_regs['rst_id'] = isset($row['rst_id']) ? $row['rst_id']: '';
                        $this->lc_regs['rst_tipo_servicio'] = isset($row['rst_tipo_servicio']) ? $row['rst_tipo_servicio']: '';
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                break;

            case 'validaEstacionActiva':
                $lc_sql = "EXECUTE seguridad.USP_validaipperfilperiodo $lc_datos[0],'$lc_datos[1]'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs['estacionActiva'] = isset($row['estacionActiva']) ? (int) $row['estacionActiva'] : '';
                        $this->lc_regs['nombreEstacion'] = isset($row['nombreEstacion']) ? $row['nombreEstacion'] : '';
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                }
                return json_encode($this->lc_regs);
                break;

            case 'validaControlEstacion':
                $lc_sql = "EXECUTE seguridad.USP_validaipperfilperiodo $lc_datos[0],'$lc_datos[1]'";

                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs['controlEstacionActivo'] = isset($row['controlEstacionActivo'])? (int) $row['controlEstacionActivo']: '';
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                }
                return json_encode($this->lc_regs);
                break;

            case 'validarUsuarioAdministrador':
                $lc_sql = "EXECUTE seguridad.USP_validaUsuarioAdmin $lc_datos[0], '$lc_datos[1]', '$lc_datos[2]','$lc_datos[3]','$lc_datos[4]'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs['admini'] = isset($row['admini']) ? (int) $row['admini'] : '';
                        $this->lc_regs['moneda'] = isset($row['moneda']) ? $row['moneda']: '';
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                }
                return json_encode($this->lc_regs);
                break;

            case 'IngresoAdministrador':
                $lc_sql = "EXECUTE [seguridad].[USP_Ingreso_Admin] $lc_datos[0], '$lc_datos[1]','$lc_datos[2]','$lc_datos[3]'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs['administrador'] = isset($row['administrador'])? $row['administrador'] : '';
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                }
                return json_encode($this->lc_regs);
                break;

            case 'InsertControlEstacionIngresoAdmin':
                $lc_sql = "EXECUTE [seguridad].[USP_Ingreso_Admin] $lc_datos[0], '$lc_datos[1]','$lc_datos[2]','$lc_datos[3]'";
                $result = $this->fn_ejecutarquery($lc_sql);
                if ($result){ return true; }else{ return false; };
                break;
            
            case 'ValidarLoginLocalmente':
                $lc_sql = "EXECUTE [seguridad].[USP_Validar_Login_Localmente] $lc_datos[0],'$lc_datos[1]'";
                $resultado=$this->fn_ejecutarquery($lc_sql);
                $row = $this->fn_leerarreglo();
                $datos=["Respuesta"=> $row['puede_iniciar_sesion'],'mensaje'=> utf8_decode($row['mensaje'])];
                return $datos;

            case 'actualizaTodasOdp':
                $lc_query = "EXEC [seguridad].[IAE_CC_ActualizaCuentasAbieras] $lc_datos[0],'$lc_datos[1]','$lc_datos[2]','$lc_datos[3]','$lc_datos[4]', '$lc_datos[5]', $lc_datos[6]";
                if ($this->fn_ejecutarquery($lc_query)) {                    
                    while ($row = $this->fn_leerarreglo()) {                        
                        $this->lc_regs[] = array(
                            "resp" => isset($row['resp'])? $row['resp'] : '',
                            "mensaje" => isset($row['mensaje'])? $row['mensaje'] : ''         
                        );
                    }
                    
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);                    
                }
                
                $this->fn_liberarecurso();
            	break;        
        }
    }

    function administracionPeriodo($accion, $ip_estacion) {
        $lc_sql = "EXECUTE [seguridad].[USP_AdministracionPeriodos] $accion, '$ip_estacion'";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs['periodo_secuencial'] = isset($row['periodo_secuencial'])? (int) $row['periodo_secuencial'] : '';
            }
            $this->lc_regs['str'] = $this->fn_numregistro();
        }
        return json_encode($this->lc_regs);
    }

    function existePeriodoSecuencial($accion, $ip_estacion) {
        $lc_sql = "EXECUTE [seguridad].[USP_AdministracionPeriodos] $accion, '$ip_estacion'";        
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs['existe_periodo'] = isset($row['existe_periodo'])? $row['existe_periodo'] : '';
                $this->lc_regs['fecha_secuencial'] = isset($row['fecha_secuencial']) ? $row['fecha_secuencial'] : '';
            }

            $this->lc_regs['str'] = $this->fn_numregistro();
        }
        return json_encode($this->lc_regs);
    }

    function cerrar_caja($ip_estacion) {
        $lc_sql = "EXECUTE [seguridad].[USP_CierraCaja] '$ip_estacion';";   
        $this->fn_ejecutarquery($lc_sql);
        return json_encode(array('resultado'=>'OK'));
    }

}