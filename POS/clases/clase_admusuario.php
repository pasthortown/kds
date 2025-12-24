<?php

///////////////////////////////////////////////////////////////////////////////
///////DESARROLLADOR: Jorge Tinoco ////////////////////////////////////////////
///////DESCRIPCION: Pantalla de cambio de clave ///////////////////////////////
///////TABLAS INVOLUCRADAS: Usuarios del Sistema //////////////////////////////
///////FECHA CREACION: 22-01-2016 /////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////

class usuario extends sql {

    function __construct() {
        parent ::__construct();
    }

    function fn_consultar($lc_sqlQuery, $lc_datos) {
        switch ($lc_sqlQuery) {
            case "actualizarClave":
                $lc_sql = "EXEC config.USP_USR_actualizacionclave " . $lc_datos[0] . ", '" . $lc_datos[1] . "', '" . utf8_decode($lc_datos[2]) . "', '" . utf8_decode($lc_datos[3]) . "', '" . utf8_decode($lc_datos[4]) . "', ''";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("Confirmar" => $row['Confirmar']);
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            case "consultarInformacionUsuario":
                $lc_sql = "EXEC config.USP_USR_informacionusuario " . $lc_datos[0] . ", '" . $lc_datos[1] . "'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("usr_usuario" => $row['usr_usuario'],
                            "usr_cedula" => $row['usr_cedula'],
                            "usr_iniciales" => $row['usr_iniciales'],
                            "usr_descripcion" => $row['usr_descripcion'],
                            "std_id" => $row['std_id'],
                            "prf_descripcion" => $row['prf_descripcion'],
                            "ultimo_ingreso" => $row['ultimo_ingreso']);
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);
        }
    }

}