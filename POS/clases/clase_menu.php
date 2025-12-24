<?php

///////////////////////////////////////////////////////////////////////////////
///////DESARROLLADOR: Jorge Tinoco ////////////////////////////////////////////
///////DESCRIPCION: Pantalla de configuración de menú /////////////////////////
///////TABLAS INVOLUCRADAS: Menu, /////////////////////////////////////////////
///////FECHA CREACION: 20-02-2015 /////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////
///////FECHA ULTIMA MODIFICACION: 21/12/2015///////////////////////////////////
///////USUARIO QUE MODIFICO: Christian Pinto //////////////////////////////////
///////DECRIPCION ULTIMO CAMBIO: Menu en Max Point Coleccion de Datos Estacion/
///////////////////////////////////////////////////////////////////////////////

class Menu extends sql {

    //private $lc_regs;
    //constructor de la clase
    function __construct() {
        parent ::__construct();
    }

    //funcion que permite armar la sentencia sql de consulta
    function fn_consultar($lc_sqlQuery, $lc_datos) {
        switch ($lc_sqlQuery) {
            case "cargaMenus":
                $lc_sql = "EXEC config.USP_Carga_Menu " . $lc_datos[0] . ", " . $lc_datos[1] . ", '" . utf8_decode(trim($lc_datos[2])) . "','" . $lc_datos[3] . "'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("menu_id" => $row['menu_id'],
                            "menu_Nombre" => utf8_encode(trim($row['menu_Nombre'])),
                            "estado" => $row['estado'],
                            "nombreClasificacion" => utf8_encode(trim($row['nombreClasificacion'])),
                            "menu_nombre_maxpoint" => utf8_encode(trim($row['menu_nombre_maxpoint'])),
                            "clasificacion" => $row['clasificacion']);
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);
                
            case "accionMenu":
                $lc_sql = "EXEC config.IAE_Configuracion_Menu " . $lc_datos[0] . ", '" . $lc_datos[1] . "', '" . utf8_decode(trim($lc_datos[2])) . "', " . $lc_datos[3] . ", '" . $lc_datos[4] . "', '" . $lc_datos[5] . "'," . $lc_datos[6];
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("menu_id" => $row['menu_id'],
                            "menu_Nombre" => utf8_encode(trim($row['menu_Nombre'])),
                            "std_id" => $row['std_id'],
                            "IDmenu" => $row['IDmenu']);
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);
                
            case "consultaClasificacion":
                $lc_sql = "EXEC [config].[USP_menuMaxPoint] $lc_datos[0]";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("cla_id" => $row['cla_id'],
                            "cla_Nombre" => utf8_encode(trim($row['cla_Nombre'])),
                            "std_id" => $row['std_id']);
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);
                
            case "guardaMenuDuplicado":
                $lc_sql = "EXEC config.IAE_Duplicar_Menu '" . $lc_datos[0] . "', '" . utf8_decode(trim($lc_datos[1])) . "', '" . $lc_datos[2] . "', '$lc_datos[3]', '$lc_datos[4]', $lc_datos[5]";
	            $result = $this->fn_ejecutarquery($lc_sql);
                if ($result){ return true; }else{ return false; };
                
            case "guardaMenuMax":
                $lc_sql = "EXEC [config].[IAE_insertaMenuMaxColeccion] '$lc_datos[0]', '" . utf8_decode(trim($lc_datos[1])) . "', '$lc_datos[2]', '$lc_datos[3]', '$lc_datos[4]', $lc_datos[5]";
	            $result = $this->fn_ejecutarquery($lc_sql);
                if ($result){ return true; }else{ return false; };
                
            case "modificaMenuMax":
                $lc_sql = "EXEC [config].[IAE_insertaMenuMaxColeccion] '$lc_datos[0]', '" . utf8_decode(trim($lc_datos[1])) . "', '$lc_datos[2]', '$lc_datos[3]', '$lc_datos[4]', $lc_datos[5]";
	            $result = $this->fn_ejecutarquery($lc_sql);
                if ($result){ return true; }else{ return false; };
        }
    }

    function cargarTodosMenus($idCadena) {
        $lc_sql = "EXEC productos.MENUS_configuraciones 0, " . $idCadena . ", '', ''";
        try {
            $this->fn_ejecutarquery($lc_sql);
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array("idMenu" => $row['idMenu']
                                        ,"menu" => utf8_encode($row['menu'])
                                        ,"idClasificacion" => $row['idClasificacion']
                                        ,"nombreMaxpoint" => utf8_encode($row['nombreMaxpoint'])
                                        ,"estado" => utf8_encode($row['estado'])
                                        ,"clasificacion" => utf8_encode($row['clasificacion'])
                                        ,"idMedio" => $row['idMedio']);
            }
            $this->lc_regs['str'] = $this->fn_numregistro();
        } catch (Exception $e) {
            return json_encode($e);
        }
        return json_encode($this->lc_regs);
    }

    function cargarClasificaciones() {
        $lc_sql = "EXEC productos.MENUS_configuraciones 3, 0, '', ''";
        try {
            $this->fn_ejecutarquery($lc_sql);
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array("idClasificacion" => $row['idClasificacion'], "clasificacion" => utf8_encode($row['clasificacion']));
            }
            $this->lc_regs['str'] = $this->fn_numregistro();
        } catch (Exception $e) {
            return json_encode($e);
        }
        return json_encode($this->lc_regs);
    }

    function guardarMenu($accion, $idCadena, $idUsuario, $idMenu, $menu, $nombreMaxpoint, $idClasificacion, $estado,$idMedio) {
        $lc_sql = "EXEC productos.MENUS_IA_menu " . $accion . ", " . $idCadena . ", '" . $idUsuario . "', '" . $idMenu . "', '" . $menu . "', '" . $nombreMaxpoint . "', '" . $idClasificacion . "','".$estado."','".$idMedio."'";
        try {
            $this->fn_ejecutarquery($lc_sql);
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array(
                    "idMenu" => $row['idMenu']
                    , "menu" => utf8_encode($row['menu'])
                    , "idClasificacion" => $row['idClasificacion']
                    , "nombreMaxpoint" => utf8_encode($row['nombreMaxpoint'])
                    , "estado" => utf8_encode($row['estado'])
                    , "clasificacion" => utf8_encode($row['clasificacion'])
                    , "idMedio" => $row['idMedio']
                    , "medio" => utf8_encode($row['medio'])
                );
            }
            $this->lc_regs['str'] = $this->fn_numregistro();
        } catch (Exception $e) {
            return json_encode($e);
        }
        return json_encode($this->lc_regs);
    }

    function cargarMenusPorEstado($idCadena, $estado) {
        $lc_sql = "EXEC productos.MENUS_configuraciones 1, " . $idCadena . ", '', '" . $estado . "'";
        try {
            $this->fn_ejecutarquery($lc_sql);
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array("idMenu" => $row['idMenu']
                                        ,"menu" => utf8_encode($row['menu'])
                                        ,"idClasificacion" => $row['idClasificacion']
                                        ,"nombreMaxpoint" => utf8_encode($row['nombreMaxpoint'])
                                        ,"estado" => utf8_encode($row['estado'])
                                        ,"clasificacion" => utf8_encode($row['clasificacion'])
                                        ,"idMedio" => $row['idMedio']
                                        ,"medio" => utf8_encode($row['medio']));
            }
            $this->lc_regs['str'] = $this->fn_numregistro();
        } catch (Exception $e) {
            return json_encode($e);
        }
        return json_encode($this->lc_regs);
    }
    
    function fn_guardarduplicacion($idCadena, $idUsuario, $idMenuOriginal, $nombreMenuDuplicado, $nombreMenuMaxPoint, $idClasificacion, $estado) {
        $lc_sql = "EXEC productos.MENUS_IA_menu 2, " . $idCadena . ", '" . $idUsuario . "', '" . $idMenuOriginal . "', '" . utf8_decode($nombreMenuDuplicado) . "', '" . utf8_decode($nombreMenuMaxPoint) . "', '" . $idClasificacion . "', '" . $estado . "'";
        try {
            $this->fn_ejecutarquery($lc_sql);
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array("idMenu" => $row['idMenu'], "menu" => utf8_encode($row['menu']), "idClasificacion" => $row['idClasificacion'], "nombreMaxpoint" => utf8_encode($row['nombreMaxpoint']), "estado" => utf8_encode($row['estado']), "clasificacion" => utf8_encode($row['clasificacion']));
            }
            $this->lc_regs['str'] = $this->fn_numregistro();
        } catch (Exception $e) {
            return json_encode($e);
        }
        return json_encode($this->lc_regs);
    }

    function cargarListaMedios($idCadena) {
        $lc_sql = "EXEC productos.MENUS_configuraciones 4, " . $idCadena . ", '', ''";
        try {
            $this->fn_ejecutarquery($lc_sql);
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array("idMedio" => $row['idMedio'], 
                                        "medio" => utf8_encode($row['medio']));
            }
            $this->lc_regs['str'] = $this->fn_numregistro();
        } catch (Exception $e) {
            return json_encode($e);
        }
        return json_encode($this->lc_regs);
    }

    function fn_IgualarTablaMenu($lc_datos){
        set_time_limit(720);

        $lc_sql = "exec [dbo].[tablasmenu_Tiendas] ".$lc_datos[0];
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array("plus_agregados" => ($row['plus_agregados']), 
                                                            "plus_agregados" => ($row['plus_agregados']), 
                                                            // "precios" => ($row['precios']), 
                                                            "menus_agregados" => ($row['menus_agregados']), 
                                                            "menu_agrupacion" => ($row['menu_agrupacion']),
                                                            "menu_categorias" => ($row['menu_categorias']),
                                                            "menu_agrupacion_producto" => ($row['menu_agrupacion_producto']),
                                                            "categorias_botones" => ($row['categorias_botones']),
                                                            "preguntas_sugeridas" => ($row['preguntas_sugeridas']),
                                                            "plu_pregunta" => ($row['plu_pregunta']),
                                                            "coleccion_usuarios" => ($row['coleccion_usuarios']),
                                                            "coleccion_datos_usuarios" => ($row['coleccion_datos_usuarios']),
                                                            "usuario_coleccion_datos" => ($row['usuario_coleccion_datos']),
                                                            "acceso_pos" => ($row['acceso_pos']),
                                                            "pantalla_pos" => ($row['pantalla_pos']),
                                                            "permiso_perfil_pos" => ($row['permiso_perfil_pos']),
                                                            "modulo" => ($row['modulo']),
                                                            "ciudad" => ($row['ciudad']),
                                                            "clasificacion" => ($row['clasificacion'])                                                            
                                                        
                                        );
            }
        }
        $this->lc_regs['str'] = $this->fn_numregistro();
        return json_encode($this->lc_regs);    
    }

    function fn_IgualarBotonesMenu($lc_datos){

        $lc_sql = "exec [dbo].[mantenimiento_tienda] 1,".$lc_datos[0].","."'$lc_datos[1]'";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array("respuesta" => ($row['respuesta']), "menu" => ($row['menu']), "respuesta" => ($row['respuesta']));
            }
        }
        $this->lc_regs['str'] = $this->fn_numregistro();
        return json_encode($this->lc_regs);       
    }




}