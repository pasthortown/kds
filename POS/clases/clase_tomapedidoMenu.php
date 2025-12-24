<?php

class menuPedido extends sql {

    //Constructor de la Clase
    function _construct() {
        parent ::_construct();
    }

    //Función que permite armar la sentencia sql de consulta
    function fn_consultar($lc_sqlQuery, $lc_datos) {
        switch ($lc_sqlQuery) {
            case "listaCategoria":
                $lc_sql = "EXEC [config].[USP_Lista_Plantilla_Menu] '$lc_datos[0]',$lc_datos[1], '$lc_datos[2]', '$lc_datos[3]','$lc_datos[4]','$lc_datos[5]'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("mag_id" => $row['mag_id']
                            , "mag_descripcion" => utf8_encode(trim($row['mag_descripcion']))
                            , "mag_colortexto" => $row['mag_colortexto']
                            , "mag_color" => $row['mag_color']);
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            case "validaPosicionCategoria":
                $lc_sql = "EXEC [config].[USP_adminTomaPedido_validaPosicion] '$lc_datos[0]','$lc_datos[1]','$lc_datos[2]','$lc_datos[3]','$lc_datos[4]','$lc_datos[5]'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs['valida'] = $row["valida"];
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                }
                return json_encode($this->lc_regs);

            case "cargaClasificacion":
                $lc_sql = "EXEC [config].[USP_Lista_Plantilla_Menu] '$lc_datos[0]',$lc_datos[1],'$lc_datos[2]','$lc_datos[3]','$lc_datos[4]','$lc_datos[5]'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("cla_id" => $row['cla_id']
                            , "nombre" => utf8_encode(trim($row['nombre'])));
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            case "listaProductos":
                $lc_sql = "EXEC [config].[USP_Lista_Plantilla_Menu] '$lc_datos[0]', $lc_datos[1],'$lc_datos[2]', '$lc_datos[3]','$lc_datos[4]','$lc_datos[5]'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("magp_id" => $row['magp_id']
                            , "magp_desc_impresion" => utf8_encode(trim($row['magp_desc_impresion']))
                            , "magp_colortexto" => $row['magp_colortexto']
                            , "magp_color" => $row['magp_color']);
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            case "cargarMenu":
                $lc_sql = "EXEC [config].[USP_Lista_Plantilla_Menu] '$lc_datos[0]', $lc_datos[1], '$lc_datos[2]', '$lc_datos[3]','$lc_datos[4]','$lc_datos[5]'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
                            "menu_id" => $row['menu_id']
                            , "cdn_id" => $row['cdn_id']
                            , "IDClasificacion" => $row['IDClasificacion']
                            , "menu_Nombre" => utf8_encode(trim($row['menu_Nombre'])));
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            case "cargarCategoria":
                $lc_sql = "EXEC [config].[USP_Lista_Plantilla_Menu] '$lc_datos[0]',$lc_datos[1], '$lc_datos[2]', '$lc_datos[3]','$lc_datos[4]','$lc_datos[5]'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("mag_id" => $row['mag_id']
                            , "mag_descripcion" => utf8_encode(trim($row['mag_descripcion']))
                            , "mag_colortexto" => $row['mag_colortexto']
                            , "mag_color" => $row['mag_color']
                            , "mag_orden" => $row['mag_orden']);
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            case "cargarProducto":
                $lc_sql = "EXEC [config].[USP_Lista_Plantilla_Menu] '$lc_datos[0]', $lc_datos[1], '$lc_datos[2]', '$lc_datos[3]','$lc_datos[4]','$lc_datos[5]'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("plu_descripcion" => utf8_encode($row['plu_descripcion'])
                            , "magp_desc_impresion" => utf8_encode(trim($row['magp_desc_impresion']))
                            , "magp_colortexto" => $row['magp_colortexto']
                            , "magp_color" => $row['magp_color']
                            , "plu_id" => $row['plu_id']
                            , "magp_id" => $row['magp_id']
                            , "mag_id" => $row['mag_id']
                            , "magp_orden" => $row['magp_orden']);
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            case "autollenarCategoria":
                $lc_sql = "EXEC [config].[IAE_Plantilla_Menu] '$lc_datos[0]', '$lc_datos[1]', '$lc_datos[2]', '$lc_datos[3]','$lc_datos[4]', '$lc_datos[5]', '$lc_datos[6]', '$lc_datos[7]'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("mag_id" => $row['mag_id']
                            , "mag_descripcion" => $row['mag_descripcion']
                            , "mag_colortexto" => $row['mag_colortexto']
                            , "mag_color" => $row['mag_color']
                            , "mag_orden" => $row['mag_orden']); //;							
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            case "actualizarCategoria":
                $lc_sql = "EXEC [config].[IAE_Plantilla_Menu] '$lc_datos[0]', '$lc_datos[1]', '$lc_datos[2]', '$lc_datos[3]', '$lc_datos[4]', '$lc_datos[5]', '$lc_datos[6]', '$lc_datos[7]'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("mag_id" => $row['mag_id']
                            , "mag_descripcion" => $row['mag_descripcion']
                            , "mag_colortexto" => $row['mag_colortexto']
                            , "mag_color" => $row['mag_color']
                            , "mag_orden" => $row['mag_orden']);
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            case "eliminarCategoria":
                $lc_sql = "EXEC [config].[IAE_Plantilla_Menu] '$lc_datos[0]', '$lc_datos[1]', '$lc_datos[2]', '$lc_datos[3]', '$lc_datos[4]', '$lc_datos[5]', '$lc_datos[6]', '$lc_datos[7]'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("mag_id" => $row['mag_id']
                            , "mag_descripcion" => $row['mag_descripcion']
                            , "mag_colortexto" => $row['mag_colortexto']
                            , "mag_color" => $row['mag_color']
                            , "mag_orden" => $row['mag_orden']);
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            case "autollenarProducto":
                $lc_sql = "EXEC [config].[IAE_Plantilla_Menu] '$lc_datos[0]', '$lc_datos[1]', '$lc_datos[2]', '$lc_datos[3]', '$lc_datos[4]', '$lc_datos[5]', '$lc_datos[6]', '$lc_datos[7]'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("magp_desc_impresion" => $row['magp_desc_impresion']
                            , "magp_colortexto" => $row['magp_colortexto']
                            , "magp_color" => $row['magp_color']
                            , "plu_id" => $row['plu_id']
                            , "magp_id" => $row['magp_id']
                            , "magp_orden" => $row['magp_orden']
                            , "mag_id" => $row['mag_id']);
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            case "actualizarProducto":
                $lc_sql = "EXEC [config].[IAE_Plantilla_Menu] '$lc_datos[0]', '$lc_datos[1]', '$lc_datos[2]', '$lc_datos[3]', '$lc_datos[4]', '$lc_datos[5]', '$lc_datos[6]', '$lc_datos[7]'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("mag_id" => $row['mag_id']
                            , "magp_desc_impresion" => $row['magp_desc_impresion']
                            , "magp_colortexto" => $row['magp_colortexto']
                            , "magp_color" => $row['magp_color']
                            , "magp_orden" => $row['magp_orden']
                            , "mag_id" => $row['mag_id']);
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            case "eliminarProducto":
                $lc_sql = "EXEC [config].[IAE_Plantilla_Menu] '$lc_datos[0]', '$lc_datos[1]', '$lc_datos[2]', '$lc_datos[3]', '$lc_datos[4]', '$lc_datos[5]', '$lc_datos[6]', '$lc_datos[7]'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("magp_desc_impresion" => $row['magp_desc_impresion']
                            , "magp_colortexto" => $row['magp_colortexto']
                            , "magp_color" => $row['magp_color']
                            , "magp_orden" => $row['magp_orden']);
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            case "actualizaMenuAgrupacion":
                $lc_sql = "EXEC [config].[IAE_Plantilla_Menu] '$lc_datos[0]', '$lc_datos[1]', '$lc_datos[2]', '$lc_datos[3]', '$lc_datos[4]', '$lc_datos[5]', '$lc_datos[6]', '$lc_datos[7]'";
                $result = $this->fn_ejecutarquery($lc_sql);
                if ($result){ return true; }else{ return false; };
        }
    }

}