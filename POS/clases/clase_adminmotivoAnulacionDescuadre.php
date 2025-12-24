<?php

////////////////////////////////////////////////////////////////////////////////////////////////////////
////////DESARROLLADO POR: CHRISTIAN PINTO///////////////////////////////////////////////////////////////
///////////DESCRIPCION: ADMINISTRACION DE MOTIVO ANULACION, LISTADO, AGREGAR Y MODIFICAR    ////////////
////////////////TABLAS: Motivo_Anulacion ////////////////////////////////////////////////////////////////
////////FECHA CREACION: 13/07/2015//////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////

class administracionmotivoanulacion extends sql {

    //private $lc_regs;
    //constructor de la clase
    function __construct() {
        parent ::__construct();
    }

    function fn_consultar($lc_sqlQuery, $lc_datos) {
        switch ($lc_sqlQuery) {

            case "administracionMotivoanulacion":
                $lc_sql = "EXECUTE config.USP_administracionmotivoanulacion $lc_datos[0], $lc_datos[1], '$lc_datos[2]', '$lc_datos[3]', '$lc_datos[4]', '$lc_datos[5]'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("mtv_id" => $row['mtv_id'],
                            "mtv_descripcion" => utf8_encode(($row['mtv_descripcion'])),
                            "std_id" => $row['std_id']);
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);
        }
    }

}