<?php

///////////////////////////////////////////////////////////
///////DESARROLLADO POR: Juan Méndez ////////////////////
///////DESCRIPCION:  ////////////////////////////  /////////  /////////  
///////TABLAS INVOLUCRADAS:Menu_Agrupacionproducto, Cadena //////// 
//////////////////Plus////////////////////////////////////  /////////  
///////USUARIO QUE MODIFICO:  Jose Fernandez//////////////////////////////
///////DECRIPCION ULTIMO CAMBIO: Buscador todos los campos///////////////////
/////////////////////////////////////////////////////////////////////////////      


class plu extends sql {

    //private $lc_regs;
    //constructor de la clase
    function __construct() {
        parent ::__construct();
    }

    //funcion que permite armar la sentencia sql de consulta

    function fn_consultar($lc_sqlQuery, $lc_datos) {
        switch ($lc_sqlQuery) {
            case "cargarCaracteristica":
                $lc_sql = "EXEC config.USP_Paginador_Botones $lc_datos[1],$lc_datos[2],$lc_datos[3], $lc_datos[0], '" . utf8_decode($lc_datos[4]) . "'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("Total" => $row['Total'],
                            "magp_id" => $row['magp_id'],
												 "plu_num_plu"=>$row['plu_num_plu'],
                            "plu_id" => $row['plu_id'],
                            "plu_descripcion" => utf8_encode(trim($row['plu_descripcion'])),
                            "magp_impresion" => utf8_encode(trim($row['magp_impresion'])),
                            "cla_Nombre" => utf8_encode(trim($row['cla_Nombre'])),
                            "magp_color" => $row['magp_color'],
                            "magp_colortexto" => $row['magp_colortexto'],
                            "magp_desc_impresion" => utf8_encode(trim($row['magp_desc_impresion'])),
                            "plu_reportnumber" => $row['plu_reportnumber'],
                            "estado" => $row['estado']);
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            case "cargarCaracteristicaPorEstado":
                $lc_sql = "EXEC config.USP_Paginador_Botones_Estado $lc_datos[2],$lc_datos[3],$lc_datos[0],'" . utf8_decode($lc_datos[4]) . "','$lc_datos[5]'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("Total" => $row['Total'],
                            "magp_id" => $row['magp_id'],
												 "plu_num_plu"=>$row['plu_num_plu'],
                            "plu_id" => $row['plu_id'],
                            "plu_descripcion" => utf8_encode(trim($row['plu_descripcion'])),
                            "magp_impresion" => utf8_encode(trim($row['magp_impresion'])),
                            "cla_Nombre" => utf8_encode(trim($row['cla_Nombre'])),
                            "magp_color" => $row['magp_color'],
                            "magp_colortexto" => $row['magp_colortexto'],
                            "magp_desc_impresion" => utf8_encode(trim($row['magp_desc_impresion'])),
                            "plu_reportnumber" => $row['plu_reportnumber'],
                            "estado" => $row['estado']);
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            case "cargarDatosPluMenu":
                $lc_sql = "EXEC config.USP_Carga_Botones $lc_datos[0], '$lc_datos[1]'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs['magp_id'] = $row["magp_id"];
                        $this->lc_regs['plu_id'] = $row["plu_id"];
                        $this->lc_regs['plu_descripcion'] = utf8_encode(trim($row["plu_descripcion"]));
                        $this->lc_regs['magp_impresion'] = utf8_encode(trim($row["magp_impresion"]));
                        $this->lc_regs['magp_color'] = $row["magp_color"];
                        $this->lc_regs['magp_colortexto'] = $row["magp_colortexto"];
                        $this->lc_regs['magp_desc_impresion'] = utf8_encode(trim($row["magp_desc_impresion"]));
                        $this->lc_regs['estado'] = $row["estado"];
                        $this->lc_regs['magp_fecha_inicio'] = $row["magp_fecha_inicio"];
                        $this->lc_regs['magp_fecha_vencimiento'] = $row["magp_fecha_vencimiento"];
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            case "cargarPlus":
                $lc_sql = "exec config.USP_Carga_Select_Plus $lc_datos[0]";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("plu_id" => $row['plu_id'],
                            "plu_num_plu" => $row['plu_num_plu'],
                            "plu_descripcion" => utf8_encode(trim($row['plu_descripcion'])),
                            "cla_Nombre" => utf8_encode(trim($row['cla_Nombre'])));
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            case "actualizaInfoPluMenu":
                $lc_sql = "SET DATEFORMAT dmy exec config.IAE_Configuracion_Botones $lc_datos[0], $lc_datos[1], '$lc_datos[2]', '$lc_datos[3]', '$lc_datos[4]', '$lc_datos[5]', '$lc_datos[6]', '$lc_datos[7]', '$lc_datos[8]', '$lc_datos[9]', '$lc_datos[10]'";
                $result = $this->fn_ejecutarquery($lc_sql);
                if ($result){ return true; }else{ return false; };

            case "guardaInfoPluMenuNuevo":
                $lc_sql = "SET DATEFORMAT dmy exec config.IAE_Configuracion_Botones $lc_datos[0], $lc_datos[1], '$lc_datos[2]', '$lc_datos[3]', '$lc_datos[4]', '$lc_datos[5]', '$lc_datos[6]', '$lc_datos[7]', '$lc_datos[8]', '$lc_datos[9]', '$lc_datos[10]'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("IDMenuAgrupacionProducto" => $row['IDMenuAgrupacionProducto']);
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            case "traerRestaurantes":
                $lc_sql = "EXEC [config].[IAE_Aplicar_Boton_Restaurante] $lc_datos[0], $lc_datos[1], '$lc_datos[2]', '$lc_datos[3]', '$lc_datos[4]', $lc_datos[5], '$lc_datos[6]'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("rst_id" => $row['rst_id'],
                            "rst_descripcion" => utf8_encode(trim($row['rst_descripcion'])),
                            "agregado" => $row['agregado']);
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);
}
}

}