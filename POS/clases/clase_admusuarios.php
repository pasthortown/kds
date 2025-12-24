<?php

///////////////////////////////////////////////////////////////////////////////
///////DESARROLLADOR: Jorge Tinoco ////////////////////////////////////////////
///////DESCRIPCION: Pantalla de configuración de usuarios /////////////////////
///////FECHA CREACION: 28-01-2016 /////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////

class usuarios extends sql {

    //constructor de la clase
    function __construct() {
        parent ::__construct();
    }

    //funcion que permite armar la sentencia sql de consulta        
    function fn_cargarPerfiles($lc_datos) {
        $lc_sql = "EXEC config.USP_SEG_configuracionusuarios " . $lc_datos[0] . ", '" . $lc_datos[1] . "', " . $lc_datos[2] . ", '" . $lc_datos[3] . "'";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array("prf_id" => $row['prf_id'],
                    "prf_descripcion" => utf8_encode($row['prf_descripcion']));
            }
        }
        $this->lc_regs['str'] = $this->fn_numregistro();
        return json_encode($this->lc_regs);
    }

    function fn_cargarUsuarios($lc_datos) {
        $lc_sql = "EXEC config.USP_SEG_configuracionusuarios " . $lc_datos[0] . ", '" . $lc_datos[1] . "', " . $lc_datos[2] . ", '" . $lc_datos[3] . "'";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array("usr_id" => $row['usr_id'], "usr_cedula" => $row['usr_cedula'],
                    "prf_id" => $row['prf_id'], "usr_fecha_ingreso" => $row['usr_fecha_ingreso'],
                    "usr_fecha_salida" => $row['usr_fecha_salida'], "usr_email" => $row['usr_email'],
                    "usr_telefono" => $row['usr_telefono'],
                    "RestauranteAsignado" => $row['RestauranteAsignado'],
                    "usr_direccion" => utf8_encode($row['usr_direccion']),
                    "usr_nombre_en_pos" => utf8_encode($row['usr_nombre_en_pos']),
                    "prf_descripcion" => utf8_encode($row['prf_descripcion']),
                    "usr_usuario" => utf8_encode($row['usr_usuario']),
                    "usr_iniciales" => $row['usr_iniciales'],
                    "usr_descripcion" => utf8_encode($row['usr_descripcion']),
                    "usr_tarjeta" => $row['usr_tarjeta'],
                    "std_id" => $row['std_id']);
            }
        }
        $this->lc_regs['str'] = $this->fn_numregistro();
        return json_encode($this->lc_regs);
    }

    function fn_cargarLocalesUsuario($lc_datos) {
        $lc_sql = "EXEC config.USP_SEG_configuracionusuarios " . $lc_datos[0] . ", '" . $lc_datos[1] . "', " . $lc_datos[2] . ", '" . $lc_datos[3] . "'";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array("rst_id" => $row['rst_id']);
            }
        }
        $this->lc_regs['str'] = $this->fn_numregistro();
        return json_encode($this->lc_regs);
    }

    function fn_cargarLocales($lc_datos) {
        $lc_sql = "EXEC config.USP_SEG_configuracionusuarios " . $lc_datos[0] . ", '" . $lc_datos[1] . "', " . $lc_datos[2] . ", '" . $lc_datos[3] . "'";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array("rst_id" => $row['rst_id'],
                    "rst_descripcion" => utf8_encode($row['rst_descripcion']));
            }
        }
        $this->lc_regs['str'] = $this->fn_numregistro();
        return json_encode($this->lc_regs);
    }

    function fn_administrarUsuario($lc_datos) {
        $lc_sql = "EXEC config.IAE_SEG_administracionusuarios " . $lc_datos[0] . ", '" . $lc_datos[1] . "', " . $lc_datos[2] . ", '" . $lc_datos[3] . "', '" . $lc_datos[4] . "', '" . $lc_datos[5] . "', '" . $lc_datos[6] . "', '" . $lc_datos[7] . "', '" . $lc_datos[8] . "', '" . $lc_datos[9] . "', '" . $lc_datos[10] . "', '" . $lc_datos[11] . "', '" . $lc_datos[12] . "', '" . $lc_datos[13] . "', '" . $lc_datos[14] . "', '" . $lc_datos[15] . "', '" . $lc_datos[16] . "', '" . $lc_datos[17] . "', '" . $lc_datos[18] . "', " . $lc_datos[19];
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array("usr_id" => $row['usr_id'], "usr_cedula" => $row['usr_cedula'],
                    "prf_id" => $row['prf_id'], "usr_fecha_ingreso" => $row['usr_fecha_ingreso'],
                    "usr_fecha_salida" => $row['usr_fecha_salida'], "usr_email" => $row['usr_email'],
                    "usr_telefono" => $row['usr_telefono'], "RestauranteAsignado" => $row['RestauranteAsignado'],
                    "usr_direccion" => utf8_encode($row['usr_direccion']),
                    "usr_nombre_en_pos" => utf8_encode($row['usr_nombre_en_pos']),
                    "prf_descripcion" => utf8_encode($row['prf_descripcion']),
                    "usr_usuario" => utf8_encode($row['usr_usuario']),
                    "usr_iniciales" => $row['usr_iniciales'],
                    "usr_descripcion" => utf8_encode($row['usr_descripcion']),
                    "usr_tarjeta" => $row['usr_tarjeta'],
                    "std_id" => $row['std_id']);
            }
        }
        $this->lc_regs['str'] = $this->fn_numregistro();
        return json_encode($this->lc_regs);
    }

    function fn_verificarUsuarioSistema($lc_datos) {
        $lc_sql = " IF NOT EXISTS(SELECT * FROM Users_Pos WHERE usr_usuario like '" . $lc_datos[0] . "') BEGIN
                        SELECT 1 Continuar;
                    END ELSE BEGIN
                        SELECT 0 Continuar;	
                    END";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array("Continuar" => $row['Continuar']);
            }
        }
        $this->lc_regs['str'] = $this->fn_numregistro();
        return json_encode($this->lc_regs);
    }

    function fn_restablecerClaveUsuario($lc_datos) {
        $lc_sql = "EXEC config.USP_USR_actualizacionclave " . $lc_datos[0] . ", '" . $lc_datos[1] . "', '" . $lc_datos[2] . "', '" . $lc_datos[3] . "', '" . $lc_datos[4] . "', '" . $lc_datos[5] . "'";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array("continuar" => $row['Confirmar']);
            }
        }
        $this->lc_regs['str'] = $this->fn_numregistro();
        return json_encode($this->lc_regs);
    }

    function fn_ValidaDocumento($lc_datos) {
        $lc_sql = "EXEC [config].[USP_ValidaCedulaUsuario] " . $lc_datos[0] . ", " . $lc_datos[1] . ", '" . $lc_datos[2] . "', '" . $lc_datos[3] . "'";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                if ($row['continuar'] == 1) {
                    $this->lc_regs[] = array("continuar" => $row['continuar'],
                        "usuario" => $row['usuario'],
                        "perfil" => $row['perfil'],
                        "tienda" => $row['tienda']);
                } else {
                    $this->lc_regs[] = array("continuar" => $row['continuar']);
                }
            }
        }
        $this->lc_regs['str'] = $this->fn_numregistro();
        return json_encode($this->lc_regs);
    }

    function fn_ValidaUsuario($lc_datos) {
        $lc_sql = "EXEC [config].[USP_ValidaCedulaUsuario] " . $lc_datos[0] . ", " . $lc_datos[1] . ", '" . $lc_datos[2] . "', '" . $lc_datos[3] . "'";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array("continuar" => $row['continuar']);
            }
        }
        $this->lc_regs['str'] = $this->fn_numregistro();
        return json_encode($this->lc_regs);
    }

    function fn_listarRegiones() {
        $lc_sql = "SELECT * FROM dbo.Region order by rgn_descripcion asc";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array("rgn_descripcion" => $row['rgn_descripcion']);
            }
        }
        $this->lc_regs['str'] = $this->fn_numregistro();
        return json_encode($this->lc_regs);
    }

}