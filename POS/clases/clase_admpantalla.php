<?php

///////////////////////////////////////////////////////////////////////////////
///////DESARROLLADOR: Jorge Tinoco ////////////////////////////////////////////
///////DESCRIPCION: Pantalla de configuración de menú /////////////////////////
///////FECHA CREACION: 25-01-2016 /////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////


class pantalla extends sql {

    //private $lc_regs;
    //constructor de la clase
    function __construct() {
        parent ::__construct();
    }

    //funcion que permite armar la sentencia sql de consulta

    function fn_consultar($lc_sqlQuery, $lc_datos) {
        switch ($lc_sqlQuery) {
            case "cargarPantallas":
                $lc_sql = "EXEC config.USP_SEG_pantalla " . $lc_datos[0] . ", '" . $lc_datos[1] . "', " . $lc_datos[2] . ", '" . $lc_datos[3] . "'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("pnt_id" => $row['pnt_id'],
                            "pnt_Nombre_Mostrar" => utf8_encode($row['pnt_Nombre_Mostrar']),
                            "pnt_Nombre_Formulario" => utf8_encode($row['pnt_Nombre_Formulario']),
                            "pnt_Descripcion" => utf8_encode($row['pnt_Descripcion']),
                            "pnt_Orden_Menu" => $row['pnt_Orden_Menu'],
                            "pnt_Nivel" => $row['pnt_Nivel'],
                            "pnt_Ruta" => utf8_encode($row['pnt_Ruta']),
                            "pnt_Imagen" => utf8_encode($row['pnt_Imagen']),
                            "std_id" => $row['std_id']);
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            case "cargarPredecesores":
                $lc_sql = "EXEC config.USP_SEG_pantalla " . $lc_datos[0] . ", '" . $lc_datos[1] . "', " . $lc_datos[2] . ", '" . $lc_datos[3] . "'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("pnt_Nombre_Mostrar" => utf8_encode($row['pnt_Nombre_Mostrar']),
                            "pnt_Orden_Menu" => $row['pnt_Orden_Menu']);
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            case "administrarPantallas":
                $lc_sql = "EXEC config.IAE_SEG_pantalla " . $lc_datos[0] . ", '" . $lc_datos[1] . "', '" . $lc_datos[2] . "', '" . $lc_datos[3] . "', '" . $lc_datos[4] . "', " . $lc_datos[5] . ", " . $lc_datos[6] . ", '" . $lc_datos[7] . "', '" . $lc_datos[8] . "', '" . $lc_datos[9] . "', '" . $lc_datos[10] . "', " . $lc_datos[11] . ", '" . $lc_datos[12] . "'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("pnt_id" => $row['pnt_id'],
                            "pnt_Nombre_Mostrar" => utf8_encode($row['pnt_Nombre_Mostrar']),
                            "pnt_Nombre_Formulario" => $row['pnt_Nombre_Formulario'],
                            "pnt_Descripcion" => $row['pnt_Descripcion'],
                            "pnt_Orden_Menu" => $row['pnt_Orden_Menu'],
                            "pnt_Nivel" => $row['pnt_Nivel'],
                            "pnt_Ruta" => $row['pnt_Ruta'],
                            "pnt_Imagen" => $row['pnt_Imagen'],
                            "std_id" => $row['std_id']);
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            case "cargarOrdenMenu":
                $lc_sql = "EXEC config.USP_SEG_pantalla " . $lc_datos[0] . ", '" . $lc_datos[1] . "', " . $lc_datos[2] . ", '" . $lc_datos[3] . "'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("pnt_Orden_Menu" => $row['pnt_Orden_Menu']);
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            case "cargarAccesos":
                $lc_sql = "EXEC config.USP_SEG_pantalla " . $lc_datos[0] . ", '" . $lc_datos[1] . "', " . $lc_datos[2] . ", '" . $lc_datos[3] . "'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("acc_id" => $row['acc_id'],
                            "acc_descripcion" => utf8_encode($row['acc_descripcion']));
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);
        }
    }

}
