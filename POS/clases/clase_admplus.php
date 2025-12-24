<?php

///////////////////////////////////////////////////////////////////////////////
///////DESARROLLADOR: Jorge Tinoco ////////////////////////////////////////////
///////DESCRIPCION: Pantalla de configuración de menú /////////////////////////
///////TABLAS INVOLUCRADAS: Menu, /////////////////////////////////////////////
///////FECHA CREACION: 23-04-2015 /////////////////////////////////////////////
///////FECHA ULTIMA MODIFICACION: /////////////////////////////////////////////
///////USUARIO QUE MODIFICO: //////////////////////////////////////////////////
///////DECRIPCION ULTIMO CAMBIO: //////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////
///////FECHA ULTIMA MODIFICACION: 04/01/2016 //////////////////////////////////
///////USUARIO QUE MODIFICO: Christian Pinto //////////////////////////////////
///////DECRIPCION ULTIMO CAMBIO: Creacion de lista dinamica de Preguntas //////
/////// Sugeridas para el orden y buscador en Lista ///////////////////////////
///////////////////////////////////////////////////////////////////////////////
///////FECHA ULTIMA MODIFICACION: 25/07/2016 
///////USUARIO QUE MODIFICO: Daniel Llerena
///////DECRIPCION ULTIMO CAMBIO: Se crea una función para cada consulta 
/////// y ejecución.
///////////////////////////////////////////////////////////////////////////////


class configuracionplus extends sql{
	//private $lc_regs;
	//constructor de la clase
	function __construct()
        {
            parent ::__construct();
	}
	        
        function fn_guardaVariosCanalesImpresion($lc_datos)
        {
            $lc_sql = "EXEC [config].[IAE_Canal_Impresion_Producto] '$lc_datos[2]', $lc_datos[1], '$lc_datos[0]', '$lc_datos[3]'";				
            $result = $this->fn_ejecutarquery($lc_sql);
            if ($result){ return true; }else{ return false; };
        }
        
        function fn_guardaImpuestos($lc_datos)
        {
            $lc_sql = "EXEC [config].[IAE_Impuestos_De_Productos] '$lc_datos[2]', $lc_datos[1], '$lc_datos[0]', '$lc_datos[3]'";				
            $result = $this->fn_ejecutarquery($lc_sql);
            if ($result){ return true; }else{ return false; };
        }
        
        function fn_cargarPlus($lc_datos)
        {
            $lc_sql = "EXEC config.USP_Paginador_Plu_Productos ".$lc_datos[0].",'".$lc_datos[1]."',".$lc_datos[2].", ".$lc_datos[3].", '".utf8_decode($lc_datos[4])."'";
            if($this->fn_ejecutarquery($lc_sql)){
                while($row = $this->fn_leerarreglo()){
                    $this->lc_regs[] = array("plu_id"=>$row['plu_id'],
                                            "magp_id"=>$row['magp_id'],
                                            "Num_Plu"=>$row['Num_Plu'],
                                            "cla_Nombre"=>trim($row['cla_Nombre']),
                                            "plu_reportnumber"=>$row['plu_reportnumber'],
                                            "magp_color"=>$row['magp_color'],
                                            "magp_colortexto"=>$row['magp_colortexto'],
                                            "plu_descripcion"=>trim($row['plu_descripcion']),
                                            "det_Impresion"=>utf8_encode(trim($row['det_Impresion'])));
                }
            }
            $this->lc_regs['str'] = $this->fn_numregistro();
            return json_encode($this->lc_regs);
        }
        
        function fn_cargarPlusXClasificacion($lc_datos)
        {
            $lc_sql = "EXEC config.USP_Paginador_Plu_Productos_Clasificacion ".$lc_datos[0].",".$lc_datos[1].",".$lc_datos[2].", ".$lc_datos[3].", '".utf8_decode($lc_datos[4])."','$lc_datos[5]'";
            if($this->fn_ejecutarquery($lc_sql)){
                while($row = $this->fn_leerarreglo()){
                    $this->lc_regs[] = array("plu_id"=>$row['plu_id'],
                                            "magp_id"=>$row['magp_id'],
                                            "Num_Plu"=>$row['Num_Plu'],
                                            "cla_Nombre"=>utf8_encode(trim($row['cla_Nombre'])),
                                            "plu_reportnumber"=>$row['plu_reportnumber'],
                                            "magp_color"=>$row['magp_color'],
                                            "magp_colortexto"=>$row['magp_colortexto'],
                                            "plu_descripcion"=>utf8_encode(trim($row['plu_descripcion'])),
                                            "det_Impresion"=>utf8_encode(trim($row['det_Impresion'])));
                }
            }
            $this->lc_regs['str'] = $this->fn_numregistro();
            return json_encode($this->lc_regs);
        }
        
        function fn_cargarConfiguracionPlus($lc_datos)
        {
            $lc_sql = "EXEC config.IAE_Configuracion_Plus ".$lc_datos[0].",".$lc_datos[1].",'".$lc_datos[2]."', ".$lc_datos[3].", '".$lc_datos[4]."',' ".$lc_datos[5]."'";
            if($this->fn_ejecutarquery($lc_sql)){
                while($row = $this->fn_leerarreglo()){
                    $this->lc_regs[] = array("anulacion"=>$row['anulacion'],
                                            "gramo"=>$row['gramo'],												 
                                            "cantidad"=>$row['cantidad'],
                                            "tiempo_preparacion"=>$row['tiempo_preparacion'],
                                            "tipo_plato"=>$row['tipo_plato'],
                                            "codigo_barras"=>$row['codigo_barras'],
                                            "plu_reportnumber"=>$row['plu_reportnumber'],												
                                            "plu_descripcion"=>htmlentities($row['plu_descripcion']),
                                            "cla_Nombre"=>htmlentities($row['cla_Nombre']),
                                            "qsr"=>$row['qsr'],
                        "estado" => $row['estado'],
                                            "qsr"=>$row['qsr']);
                }
            }
            $this->lc_regs['str'] = $this->fn_numregistro();
            return json_encode($this->lc_regs);
        }
        
        function fn_cargarCanalImpresion($lc_datos)
        {
            $lc_sql = "EXEC config.IAE_Configuracion_Plus ".$lc_datos[0].",".$lc_datos[1].",'".$lc_datos[2]."', ".$lc_datos[3].", '".$lc_datos[4]."', '".$lc_datos[5]."'";
            if($this->fn_ejecutarquery($lc_sql)){
                while($row = $this->fn_leerarreglo()){
                    $this->lc_regs[] = array("canal_impresion"=>$row['canal_impresion']);
                }
            }
            $this->lc_regs['str'] = $this->fn_numregistro();
            return json_encode($this->lc_regs);  
        }
        
        function fn_cargarImpuestosProducto($lc_datos)
        {
            $lc_sql = "EXEC config.IAE_Configuracion_Plus ".$lc_datos[0].",".$lc_datos[1].",'".$lc_datos[2]."', ".$lc_datos[3].", '".$lc_datos[4]."', '".$lc_datos[5]."'";
            if($this->fn_ejecutarquery($lc_sql)){
                while($row = $this->fn_leerarreglo()){
                    $this->lc_regs[] = array("canal_impresion"=>$row['canal_impresion']);
                }
            }
            $this->lc_regs['str'] = $this->fn_numregistro();
            return json_encode($this->lc_regs);
        }
        
        function fn_cargarImpuestos($lc_datos)
        {
            $lc_sql = "Set ANSI_NULLS ON
                       Set ANSI_WARNINGS ON EXEC config.USP_Carga_Configuracion_Plus  ".$lc_datos[0].",".$lc_datos[1].",".$lc_datos[2];
            if($this->fn_ejecutarquery($lc_sql)){
                while($row = $this->fn_leerarreglo()){
                    $this->lc_regs[] = array("IDImpuestos"=>utf8_encode(trim($row['IDImpuestos'])),
                                            "nombreImpuesto"=>utf8_encode(trim($row['nombreImpuesto'])),
                                            "idIntegracion"=>$row['idIntegracion']);
                }
            }
            $this->lc_regs['str'] = $this->fn_numregistro();
            return json_encode($this->lc_regs);
        }
        
        function fn_cargarPreguntasPlus($lc_datos)
        {
            $lc_sql = "EXEC config.USP_Preguntas_Sugeridas_Plus ".$lc_datos[0].",".$lc_datos[1].",".$lc_datos[2].",".$lc_datos[3].",'".$lc_datos[4]."','".$lc_datos[5]."',".$lc_datos[6];
            if($this->fn_ejecutarquery($lc_sql)){
                while($row = $this->fn_leerarreglo()){
                    $this->lc_regs[] = array("psug_id"=>$row['psug_id'],
                                            "pre_sug_descripcion"=>utf8_encode(trim($row['pre_sug_descripcion'])));
                }
            }
            $this->lc_regs['str'] = $this->fn_numregistro();
            return json_encode($this->lc_regs);
        }
        
        function fn_cargarPreguntasNoAgregadasPlus($lc_datos)
        {
            $lc_sql = "EXEC config.USP_Preguntas_Sugeridas_Plus ".$lc_datos[0].",".$lc_datos[1].",".$lc_datos[2].",".$lc_datos[3].",'".$lc_datos[4]."','".$lc_datos[5]."',".$lc_datos[6];
            if($this->fn_ejecutarquery($lc_sql)){
                while($row = $this->fn_leerarreglo()){
                    $this->lc_regs[] = array("psug_id"=>$row['psug_id'],
                                            "pre_sug_descripcion"=>utf8_encode(trim($row['pre_sug_descripcion'])));
                }
            }
            $this->lc_regs['str'] = $this->fn_numregistro();
            return json_encode($this->lc_regs);
        }
        
        function fn_agregarPreguntasPlus($lc_datos)
        {
            $lc_sql="EXEC config.USP_Preguntas_Sugeridas_Plus ".$lc_datos[0].", ".$lc_datos[1].", ".$lc_datos[2].", ".$lc_datos[3].", '".$lc_datos[4]."', '".$lc_datos[5]."' ,".$lc_datos[6];
            if($this->fn_ejecutarquery($lc_sql)){
                while($row = $this->fn_leerarreglo()){
                    $this->lc_regs[] = array("psug_id"=>$row['psug_id'],
                                            "pre_sug_descripcion"=>utf8_encode(trim($row['pre_sug_descripcion'])));
                }
            }
            $this->lc_regs['str'] = $this->fn_numregistro();
            return json_encode($this->lc_regs);
        }

        function fn_configuracionPlu($lc_datos)
        {
            $lc_sql = "EXEC config.IAE_Configuracion_Plus ".$lc_datos[0].", ".$lc_datos[1].", '".$lc_datos[2]."', ".$lc_datos[3].", '".$lc_datos[4]."', '".$lc_datos[5]."'";
            $result = $this->fn_ejecutarquery($lc_sql);
            if ($result){ return true; }else{ return false; };
        }
        
        function fn_cargarClasificacion($lc_datos)
        {
            $lc_sql = "Set ANSI_NULLS ON
                       Set ANSI_WARNINGS ON EXEC config.USP_Carga_Configuracion_Plus ".$lc_datos[0].",".$lc_datos[1].",'".$lc_datos[2]."'";
           if($this->fn_ejecutarquery($lc_sql)){
                while($row = $this->fn_leerarreglo()){
                    $this->lc_regs[] = array("cla_id"=>$row['cla_id'],
                                            "cla_Nombre"=>utf8_encode(trim($row['cla_Nombre'])));
                }
           }
           $this->lc_regs['str'] = $this->fn_numregistro();
           return json_encode($this->lc_regs);
        }
        
        function fn_cargarCategoria_Plus($lc_datos)
        {
            $lc_sql = "Set ANSI_NULLS ON
                       Set ANSI_WARNINGS ON EXEC config.USP_Carga_Configuracion_Plus ".$lc_datos[0].",".$lc_datos[1].",".$lc_datos[2];
            if($this->fn_ejecutarquery($lc_sql)){
                while($row = $this->fn_leerarreglo()){
                    $this->lc_regs[] = array("Cod_Categoria"=>$row['Cod_Categoria'],
                                            "Descripcion"=>utf8_encode(trim($row['Descripcion'])));
                }
           }
           $this->lc_regs['str'] = $this->fn_numregistro();
           return json_encode($this->lc_regs);
        }
        
        function fn_cargarUbicacion($lc_datos)
        {
            $lc_sql = "Set ANSI_NULLS ON
                       Set ANSI_WARNINGS ON EXEC config.USP_Carga_Configuracion_Plus ".$lc_datos[0].",".$lc_datos[1].",".$lc_datos[2];
            if($this->fn_ejecutarquery($lc_sql)){
                while($row = $this->fn_leerarreglo()){
                    $this->lc_regs[] = array("Cod_Ubicacion_Cadenas"=>$row['Cod_Ubicacion_Cadenas'],
                                            "Descripcion"=>utf8_encode(trim($row['Descripcion'])));
                }
            }
            $this->lc_regs['str'] = $this->fn_numregistro();
            return json_encode($this->lc_regs);
        }
        
        function fn_cargarImpresora($lc_datos)
        {
            $lc_sql = "Set ANSI_NULLS ON
                       Set ANSI_WARNINGS ON EXEC config.USP_Carga_Configuracion_Plus ".$lc_datos[0].",".$lc_datos[1].",".$lc_datos[2];
            if($this->fn_ejecutarquery($lc_sql)){
                while($row = $this->fn_leerarreglo()){
                       $this->lc_regs[] = array("cimp_id"=>$row['cimp_id'],
                                                "cimp_descripcion"=>utf8_encode(trim($row['cimp_descripcion'])));
                }
            }
            $this->lc_regs['str'] = $this->fn_numregistro();
            return json_encode($this->lc_regs);
        }
        
        function fn_buscadescripcionplu($lc_datos)
        {
            $lc_sql = "Set ANSI_NULLS ON
                       Set ANSI_WARNINGS ON EXEC config.USP_Carga_Configuracion_Plus ".$lc_datos[0].",".$lc_datos[1].",".$lc_datos[2];
            if($this->fn_ejecutarquery($lc_sql)){
                while($row = $this->fn_leerarreglo()){
                    $this->lc_regs[] = array("plu_num_plu"=>$row['plu_num_plu'],
                                            "plu_descripcion"=>utf8_encode(trim($row['plu_descripcion'])),
                                            "cla_Nombre"=>utf8_encode(trim($row['cla_Nombre'])));
                }
            }
            $this->lc_regs['str'] = $this->fn_numregistro();
            return json_encode($this->lc_regs);
        }
        
        function fn_cargarBotonPlu($lc_datos)
        {
            $lc_sql = "Set ANSI_NULLS ON
                       Set ANSI_WARNINGS ON EXEC config.USP_Carga_Configuracion_Plus ".$lc_datos[0].",".$lc_datos[1].",'".$lc_datos[2]."'";
            if($this->fn_ejecutarquery($lc_sql)){
                while($row = $this->fn_leerarreglo()){
                    $this->lc_regs[] = array("magp_id"=>$row['magp_id'],
                                            "magp_color"=>$row['magp_color'],
                                            "magp_colortexto"=>$row['magp_colortexto'],												 
                                            "magp_desc_impresion"=>utf8_encode(trim($row['magp_desc_impresion'])));
                }
            }
            $this->lc_regs['str'] = $this->fn_numregistro();
            return json_encode($this->lc_regs);
        }
        
        function fn_cargarRecetas($lc_datos)
        {
            $lc_sql = "Set ANSI_NULLS ON
                       Set ANSI_WARNINGS ON EXEC config.USP_Reporte_Receta ".$lc_datos[0].", ".$lc_datos[1].", ".$lc_datos[2].", ".$lc_datos[3].", ".$lc_datos[4].", ".$lc_datos[5];
            if($this->fn_ejecutarquery($lc_sql)){
                    while($row = $this->fn_leerarreglo()){
                            $this->lc_regs[] = array("Cod_Plu"=>$row['Cod_Plu'], "Num_Plu"=>$row['Num_Plu'], "nombre_plu"=>utf8_encode(trim($row['nombre_plu'])),
                                                    "Cod_Art"=>$row['Cod_Art'], "Nombre"=>utf8_encode(trim($row['Nombre'])), "Unidad_Receta"=>utf8_encode(trim($row['Unidad_Receta'])),
                                                    "Cantidad"=>$row['Cantidad'], "CostoReceta"=>$row['CostoReceta'], "costo_total"=>$row['costo_total'],
                                                    "clase"=>utf8_encode(trim($row['clase'])), "pvp"=>$row['pvp'], "impuesto"=>$row['impuesto'],
                                                    "costo_final"=>$row['costo_final'], "contribucion"=>$row['contribucion'], "contribucion_costo"=>$row['contribucion_costo'],
                                                    "costo_porcentaje"=>$row['costo_porcentaje'],
                                                    "Total_costo_porcentaje"=>$row['Total_costo_porcentaje'],
                                                    "neto"=>$row['neto'],
                                                    "cod_categoria"=>$row['cod_categoria'],
                                                    "departamento"=>utf8_encode(trim($row['departamento'])),
                                                    "Cod_Ubicacion_Cadenas"=>$row['Cod_Ubicacion_Cadenas'],
                                                    "Descripcion"=>utf8_encode(trim($row['Descripcion'])));
                    }
            }
            $this->lc_regs['str'] = $this->fn_numregistro();
            return json_encode($this->lc_regs);
        }
        
        function fn_cargarPlatos($lc_datos)
        {
            $lc_sql = "EXEC config.IAE_Tipo_de_Plato ".$lc_datos[0].", ".$lc_datos[1].", ".$lc_datos[2].", '".$lc_datos[3]."', '".$lc_datos[4]."'";
            if($this->fn_ejecutarquery($lc_sql)){
                while($row = $this->fn_leerarreglo()){
                    $this->lc_regs[] = array("ID_ColeccionPlus"=>$row['ID_ColeccionPlus'],
                                            "ID_ColeccionDeDatosPlus"=>$row['ID_ColeccionDeDatosPlus'],
                                            "Cadena"=>$row['Cadena'],
                                            "Descripcion"=>utf8_encode(trim($row['Descripcion'])),
                                            "ESTADO"=>$row['ESTADO']);
                }
            }
            $this->lc_regs['str'] = $this->fn_numregistro();
            return json_encode($this->lc_regs);
        }
        
        function fn_guardarPlatos($lc_datos)
        {
            $lc_sqla = "EXEC config.IAE_Tipo_de_Plato ".$lc_datos[0].", ".$lc_datos[1].", ".$lc_datos[2].", '".$lc_datos[3]."', '".$lc_datos[4]."'";				
            $result = $this->fn_ejecutarquery($lc_sqla);
            if ($result){ return true; }else{ return false; };
        }
        
        function fn_cargarSeguridades($lc_datos)
        {
            $lc_sql = "EXEC [config].[IAE_Nivel_Seguridad_Plus] ".$lc_datos[0].", ".$lc_datos[1].", ".$lc_datos[2].", '".$lc_datos[3]."', '".$lc_datos[4]."'";
            if($this->fn_ejecutarquery($lc_sql)){
                    while($row = $this->fn_leerarreglo()){
                            $this->lc_regs[] = array("ID_ColeccionPlus"=>$row['ID_ColeccionPlus'],
                                                    "ID_ColeccionDeDatosPlus"=>$row['ID_ColeccionDeDatosPlus'],
                                                    "Cadena"=>$row['Cadena'],												
                                                    "Descripcion"=>utf8_encode(trim($row['Descripcion'])),
                                                    "ESTADO"=>$row['ESTADO']);
                    }
            }
            $this->lc_regs['str'] = $this->fn_numregistro();
            return json_encode($this->lc_regs);
        }
        
        function fn_guardarSeguridades($lc_datos)
        {
            $lc_sqlb = "EXEC [config].[IAE_Nivel_Seguridad_Plus] ".$lc_datos[0].", ".$lc_datos[1].", ".$lc_datos[2].", '".$lc_datos[3]."', '".$lc_datos[4]."'";				
            $result = $this->fn_ejecutarquery($lc_sqlb);
            if ($result){ return true; }else{ return false; };
        }
        
        function  fn_cargarUsuarioRecetas($lc_datos)
        {
            $lc_sql = "Set ANSI_NULLS ON
                       Set ANSI_WARNINGS ON EXEC config.USP_Usuario_Receta ".$lc_datos[0].", '".$lc_datos[1]."'";
            if($this->fn_ejecutarquery($lc_sql)){
                   while($row = $this->fn_leerarreglo()){
                           $this->lc_regs[] = array("rst_id"=>$row['rst_id'],
                                                    "rst_cod_tienda"=>$row['rst_cod_tienda'],
                                                    "rst_descripcion"=>utf8_encode(trim($row['rst_descripcion'])));
                   }
            }
            $this->lc_regs['str'] = $this->fn_numregistro();
            return json_encode($this->lc_regs);
        }
        
        function fn_sincronizarproductos($lc_datos)
        {
            $lc_sqlc = "Set ANSI_NULLS ON
                        Set ANSI_WARNINGS ON EXEC [config].[SG_Plus] ".$lc_datos[0];				
            $result = $this->fn_ejecutarquery($lc_sqlc);
            if ($result){ return true; }else{ return false; };
        }
        
        function fn_cargarTipoproducto($lc_datos)
        {
            $lc_sql = "EXEC config.[IAE_Tipo_de_Producto] ".$lc_datos[0].", ".$lc_datos[1].", ".$lc_datos[2].", '".$lc_datos[3]."','".$lc_datos[4]."'";
            if($this->fn_ejecutarquery($lc_sql)){
                while($row = $this->fn_leerarreglo()){
                    $this->lc_regs[] = array("ID_ColeccionPlus"=>$row['ID_ColeccionPlus'],
                                            "ID_ColeccionDeDatosPlus"=>$row['ID_ColeccionDeDatosPlus'],
                                            "Cadena"=>$row['Cadena'],
                                            "Descripcion"=>utf8_encode(trim($row['Descripcion'])),
                                            "ESTADO"=>$row['ESTADO']);
                }
            }
            $this->lc_regs['str'] = $this->fn_numregistro();
            return json_encode($this->lc_regs);
        }
        
        function fn_guardarTipoproducto($lc_datos)
        {
            $lc_sqld = "EXEC config.IAE_Tipo_de_Producto ".$lc_datos[0].", ".$lc_datos[1].", ".$lc_datos[2].", '".$lc_datos[3]."','".$lc_datos[4]."'";				
            $result = $this->fn_ejecutarquery($lc_sqld);
            if ($result){ return true; }else{ return false; };
        }
        
}
			