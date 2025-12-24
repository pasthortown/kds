<?php

///////////////////////////////////////////////////////////////////////////////
////////DESARROLLADO POR: CHRISTIAN PINTO////////////////////////////////////////
///////////DESCRIPCION: CLASE IMPUESTOS, CREAR MODIFICAR IMPUESTO ///////////////
////////////////TABLAS: impuestos ///////////////////////////////////////////////
////////FECHA CREACION: 10/03/2016///////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////


class impuestos extends sql {

    //private $lc_regs;
    //constructor de la clase
    function __construct() {
        parent ::__construct();
    }

    function fn_consultar($lc_sqlQuery, $lc_datos) {
        switch ($lc_sqlQuery) {
            case "cargarImpuestos":
                $lc_sql = "EXECUTE config.USP_adminImpuestos $lc_datos[0], '$lc_datos[1]', '$lc_datos[2]'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("IDImpuestos" => $row['IDImpuestos'],
                            "pais_id" => $row['pais_id'],
                            "pais_descripcion" => $row['pais_descripcion'],
                            "imp_descripcion" => $row['imp_descripcion'],
                            "imp_porcentaje" => utf8_encode(trim($row['imp_porcentaje'])),
                            "fe_codigo" => $row['fe_codigo'],
                            "fe_codigoPorcentaje" => $row['fe_codigoPorcentaje'],
                            "estado" => $row['estado']);
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            case "cargarImpuestosMod":
                $lc_sql = "EXECUTE config.USP_adminImpuestos $lc_datos[0], '$lc_datos[1]', '$lc_datos[2]'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("IDImpuestos" => $row['IDImpuestos'],
                            "pais_id" => $row['pais_id'],
                            "pais_descripcion" => $row['pais_descripcion'],
                            "imp_descripcion" => $row['imp_descripcion'],
                            "imp_porcentaje" => utf8_encode(trim($row['imp_porcentaje'])),
                            "fe_codigo" => $row['fe_codigo'],
                            "fe_codigoPorcentaje" => $row['fe_codigoPorcentaje'],
                            "estado" => $row['estado']);
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            case "numeroMaximoImpuestos":
                $lc_sql = "EXECUTE config.USP_adminImpuestos $lc_datos[0], '$lc_datos[1]', '$lc_datos[2]'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("numImpuestos" => $row['numImpuestos']);
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            case "cargarPais":
                $lc_sql = "EXECUTE config.USP_adminImpuestos $lc_datos[0], '$lc_datos[1]'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("pais_id" => $row['pais_id'],
                            "pais_descripcion" => $row['pais_descripcion']);
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);
        }
    }

    function fn_ejecutar($lc_sqlQuery, $lc_datos) {
        switch ($lc_sqlQuery) {
            case "guardarImpuesto":
                $lc_sql = "EXECUTE config.IAE_adminImpuestos '$lc_datos[0]', $lc_datos[1], '$lc_datos[2]', $lc_datos[3], '$lc_datos[4]', '$lc_datos[5]', $lc_datos[6], $lc_datos[7], '$lc_datos[8]',$lc_datos[9]";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("accion" => $row['accion']);
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);
        }
    }

}