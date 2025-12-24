<?php

///////////////////////////////////////////////////////////////////////////////
///////DESARROLLADOR: Jorge Tinoco ////////////////////////////////////////////
///////DESCRIPCION: Pantalla de configuración de perfiles /////////////////////
///////FECHA CREACION: 28-01-2016 /////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////


class perfil extends sql {

    //private $lc_regs;
    //constructor de la clase
    function __construct() {
        parent ::__construct();
    }

    //funcion que permite armar la sentencia sql de consulta

    function fn_consultar($lc_sqlQuery, $lc_datos) {
        switch ($lc_sqlQuery) {
            case "cargarPerfiles":
                $lc_sql = "EXEC [config].[USP_cargainformacionseguridades] " . $lc_datos[0] . ", " . $lc_datos[1] . ", " . $lc_datos[2] . ", '" . $lc_datos[3] . "'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("prf_id" => $row['prf_id'],
                            "prf_descripcion" => utf8_encode($row['prf_descripcion']),
                            "prf_nivel" => $row['prf_nivel'],
                            "prf_acceso" => utf8_encode($row['prf_acceso']),
                            "std_id" => $row['std_id'],
                            "esCajero" => utf8_encode($row['esCajero']));
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            case "actualizarPerfil":
                $lc_sql = "EXEC [config].[USP_perfil] " . $lc_datos[0] . ", '" . $lc_datos[1] . "', '" . utf8_decode($lc_datos[2]) . "', " . $lc_datos[3] . ", '" . utf8_decode($lc_datos[4]) . "', '" . $lc_datos[5] . "', '" . $lc_datos[6] . "', " . $lc_datos[7];
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("prf_id" => $row['prf_id'],
                            "prf_descripcion" => utf8_encode($row['prf_descripcion']),
                            "prf_nivel" => $row['prf_nivel'],
                            "prf_acceso" => utf8_encode($row['prf_acceso']),
                            "std_id" => $row['std_id'],
                            "esCajero" => utf8_encode($row['esCajero']));
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            case "verPantallasPerfil":
                $lc_sql = "EXEC [config].[USP_prf_accesopantalla] " . $lc_datos[0] . ", '" . $lc_datos[1] . "', " . $lc_datos[2] . ", '" . $lc_datos[3] . "', '" . $lc_datos[4] . "'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("pnt_id" => $row['pnt_id'],
                            "pnt_nombre_mostrar" => utf8_encode($row['pnt_nombre_mostrar']));
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            case "verAccesosPerfil":
                $lc_sql = "EXEC [config].[USP_prf_accesopantalla] " . $lc_datos[0] . ", '" . $lc_datos[1] . "', " . $lc_datos[2] . ", '" . $lc_datos[3] . "', '" . $lc_datos[4] . "'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("acc_id" => $row['acc_id'],
                            "acc_nivel" => $row['acc_nivel'],
                            "acc_descripcion" => utf8_encode($row['acc_descripcion']));
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            case "restablecerClavePerfil":
                $lc_sql = "EXEC [config].[USP_ActualizaClave] " . $lc_datos[0] . ", '" . $lc_datos[4] . "', '" . $lc_datos[2] . "', '" . $lc_datos[3] . "', '" . $lc_datos[1] . "'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    $this->lc_regs['Confirmar'] = 1;
                } else {
                    $this->lc_regs['Confirmar'] = 0;
                }
                return json_encode($this->lc_regs);
        }
    }

}
