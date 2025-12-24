<?php

///////////////////////////////////////////////////////////////////////////////
///////DESARROLLADOR: Jorge Tinoco ////////////////////////////////////////////
///////DESCRIPCION: Pantalla de configuración de menú /////////////////////////
///////TABLAS INVOLUCRADAS: Menu, /////////////////////////////////////////////
///////FECHA CREACION: 07-06-2015 /////////////////////////////////////////////
///////FECHA ULTIMA MODIFICACION: 12/09/2018 //////////////////////////////////
///////USUARIO QUE MODIFICO: Eduardo Valencia /////////////////////////////////
///////DECRIPCION ULTIMO CAMBIO: Se añadieron funciones para consumir SP del////
//////////////////////////////// módulo de una promoción /////////////////////


class descuentos extends sql {

    function __construct() {
        parent ::__construct();
    }

    function fn_consultarListaDescuentos($lc_datos){
        $lc_sql = "EXEC [Descuentos].[Descuentos]  " . $lc_datos[0] . ", " . $lc_datos[1] . ", " . $lc_datos[2];
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $retorno[] = array("dsct_id" => $row['IDDescuentos'],
                    "dsct_descripcion" => utf8_encode(trim($row['dsct_descripcion'])),
                    "tpd_descripcion" => utf8_encode(trim($row['tpd_descripcion'])),
                    "apld_descripcion" => utf8_encode(trim($row['apld_descripcion'])),
                    "fechainicio" => $row['fechainicio'],
                    "fechafin" => $row['fechafin'],
                    "std_id" => $row['IDStatus'],
                    "tpd_id" => $row['IDTipoDescuento'],
                    "apld_id" => $row['apld_id'],
                    "dsct_valor" => $row['dsct_valor'],
                    "num_reg" => $row['num_reg'],
                    "usuario_crea" => $row['UsuarioCrea'],
                    "usuario_modifica" => $row['UsuarioModifica']);
            }
        }
        $retorno['str'] = $this->fn_numregistro();
        return $retorno;
    }
    function fn_consultar($lc_sqlQuery, $lc_datos) {
        $this->lc_regs = array();
        switch ($lc_sqlQuery) {
            case "consultarListaDescuentos":
                return json_encode($this->fn_consultarListaDescuentos($lc_datos));
            case "cargarDetalleDescuento":
                $lc_sql = "EXEC [Descuentos].[Descuentos]  @resultado=" . $lc_datos["resultado"] . ", @dsct_id='" . $lc_datos["dsct_id"] . "', @cdn_id=" . $lc_datos["cdn_id"];
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
                            "IDTipoDescuento" => $row["IDTipoDescuento"],
                            "dsct_valor" => $row["dsct_valor"],
                            "dsct_descripcion" => utf8_encode($row["dsct_descripcion"]),
                            "fechainicio" => $row["fechainicio"],
                            "fechafin" => $row["fechafin"],
                            "dsct_aplica_cantidad" => $row["dsct_aplica_cantidad"],
                            "dsct_minimo" => $row["dsct_minimo"],
                            "dsct_maximo" => $row["dsct_maximo"],
                            "dsct_automatico" => $row["dsct_automatico"],
                            "dsct_seguridad" => $row["dsct_seguridad"],
                            "apld_id" => $row["apld_id"],
                            "dsct_aplica_min_max" => $row["dsct_aplica_min_max"],
                            "estado" => $row["estado"],
                            "dsct_cupones" => $row["dsct_cupones"]
                        );
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);
            case "cargarPlusDescuento":
                $lc_sql = "EXEC [Descuentos].[Descuentos]  " . $lc_datos[0] . ", '" . $lc_datos[1] . "', " . $lc_datos[2];
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("plu_id" => $row['plu_id'],
                            "plu_descripcion" => utf8_encode(trim($row['plu_descripcion'])),
                            "agregado" => $row['agregado'],
                            "plu_num_plu" => $row['plu_num_plu']);
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);
                break;
            case "cargarRestaurantesNoAsignadosDescuentos":
                $lc_sql = "EXEC [Descuentos].[Descuentos]  " . $lc_datos[0] . ", '" . $lc_datos[1] . "', " . $lc_datos[2];
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("rst_id" => $row['rst_id'],
                            "rst_descripcion" => utf8_encode(trim($row['rst_descripcion'])),
                            "agregado" => $row['agregado']);
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);
                break;
            case "cargarRestaurantesAsignadosDescuentos":
                $lc_sql = "EXEC [Descuentos].[Descuentos]  " . $lc_datos[0] . ", '" . $lc_datos[1] . "', " . $lc_datos[2];
                //echo($lc_sql);
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
                            "rst_id" => $row['rst_id'],
                            "rst_descripcion" => utf8_encode(trim($row['rst_descripcion'])),
                            "fechainicio" => utf8_encode(trim($row['fechainicio'])),
                            "fechafin" => utf8_encode(trim($row['fechafin'])),
                            "agregado" => $row['agregado']);
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);
                break;
			///////////////////////////////////	
			//// AÑADIDO PARA PROMOCIONES /////
			///////////////////////////////////	
            case "cargarRestaurantesNoAsignadosPromociones":
                $lc_sql = "EXEC [promociones].[RestaurantesNoActivos_Promociones]  " . $lc_datos[0] . ", '" . $lc_datos[1] . "'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("rst_id" => $row['rst_id'],
                            "rst_descripcion" => utf8_encode(trim($row['rst_descripcion'])),
                            "agregado" => $row['agregado']);
                    }
                }
				else
				{
				print_r(sqlsrv_errors());
				}
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);
                break;
			///////////////////////////////////	
			//// AÑADIDO PARA PROMOCIONES /////
			///////////////////////////////////	
            case "cargarClasificacion":
                $lc_sql = "EXEC [promociones].[Clasificacion] ";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("IDClasificacion" => $row['IDClasificacion'],
                            "cla_Nombre" => utf8_encode(trim($row['cla_Nombre'])));
                    }
                }
				else
				{
				print_r(sqlsrv_errors());
				}
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);
                break;				
			///////////////////////////////////	
			//// AÑADIDO PARA PROMOCIONES /////
			///////////////////////////////////		
           case "cargarCiudades":
                $lc_sql = "EXEC [promociones].[Ciudades] ". $lc_datos[2];
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("ciu_id" => $row['ciu_id'],
                            "ciu_nombre" => utf8_encode(trim($row['ciu_nombre'])),
                            "agregado" => $row['agregado']);
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);
                break;	
			///////////////////////////////////	
			//// AÑADIDO PARA PROMOCIONES /////
			///////////////////////////////////						
 			case "cargarRegiones":
                $lc_sql = "EXEC [promociones].[Regiones]";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("rgn_id" => $row['rgn_id'],
                            "rgn_descripcion" => utf8_encode(trim($row['rgn_descripcion'])),
                            "agregado" => $row['agregado']);
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);
                break;	
			///////////////////////////////////	
			//// AÑADIDO PARA PROMOCIONES /////
			///////////////////////////////////					
 			case "cargarRestaurantesCiudades":
                $lc_sql = "EXEC [promociones].[RestaurantesCiudades] " . $lc_datos[3] . ", " . $lc_datos[2];
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("rst_id" => $row['rst_id'],
                            "rst_descripcion" => utf8_encode(trim($row['rst_descripcion'])),
                            "agregado" => $row['agregado']);
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);
                break;	
			///////////////////////////////////	
			//// AÑADIDO PARA PROMOCIONES /////
			///////////////////////////////////					
 			case "cargarRestaurantesCiudadesTotal":
                $lc_sql = "EXEC [promociones].[RestaurantesCiudadesTotal] " . $lc_datos[0];
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("rst_id" => $row['rst_id'],
                            "rst_descripcion" => utf8_encode(trim($row['rst_descripcion'])),
                            "agregado" => $row['agregado']);
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);
                break;
			///////////////////////////////////	
			//// AÑADIDO PARA PROMOCIONES /////
			///////////////////////////////////					
 			case "cargarRestaurantesPromociones":
                $lc_sql = "EXEC [promociones].[Restaurantes_Promociones] ".  "'" . $lc_datos[0] . "' , ". $lc_datos[1];
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("rst_id" => $row['rst_id'],
                            "rst_descripcion" => utf8_encode(trim($row['rst_descripcion'])),
                            "agregado" => $row['agregado']);
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);
                break;		
			///////////////////////////////////	
			//// AÑADIDO PARA PROMOCIONES /////
			///////////////////////////////////					
 			case "cargarPlusBeneficiosPromocion":
                $lc_sql = "EXEC [promociones].[Beneficios_Promociones] ".  "'" . $lc_datos[0] . "'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
                            "plu_id" => $row['plu_id'],
                            "plu_descripcion" => utf8_encode(trim($row['plu_descripcion'])),
							"plu_num_plu" => $row['plu_num_plu'],
							"cantidad_plu" => $row['cantidad_plu'],
							"Tipo_aplica" => utf8_encode(trim($row['Tipo_aplica'])),
							"IDDescuento" => $row['IDDescuento'],														
                            "agregado" => $row['agregado'],
                            "productoAutoconsumo" => $row['productoAutoconsumo'],
                            "id_beneficio" => $row['id_beneficio']
                        );
                    }
                }
				else
				{
				print_r(sqlsrv_errors());
				}
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);
                break;			
			///////////////////////////////////	
			//// AÑADIDO PARA PROMOCIONES /////
			///////////////////////////////////															
			case "cargarRestaurantesRegiones":
                $lc_sql = "EXEC [promociones].[RestaurantesRegiones] " . $lc_datos[3] . ", " . $lc_datos[2];
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("rst_id" => $row['rst_id'],
                            "rst_descripcion" => utf8_encode(trim($row['rst_descripcion'])),
                            "agregado" => $row['agregado']);
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);
                break;				
			///////////////////////////////////	
			//// AÑADIDO PARA PROMOCIONES /////
			///////////////////////////////////										
			case "cargarBeneficiosPromociones":
                $lc_sql = "EXEC [promociones].[Regiones]  ";
               // echo($lc_sql);
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("rgn_id" => $row['rgn_id'],
                            "rgn_descripcion" => utf8_encode(trim($row['rgn_descripcion'])),
                            "agregado" => $row['agregado'],
                            "pais_id" => $row['pais_id']);
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);
                break;	
			///////////////////////////////////	
			//// AÑADIDO PARA PROMOCIONES /////
			///////////////////////////////////					
			case "cargarRestaurantesTotal":
                $lc_sql = "EXEC  [promociones].[RestaurantesCiudadesTotal] ". $lc_datos[0];
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("rst_id" => $row['rst_id'],
                            "rst_descripcion" => utf8_encode(trim($row['rst_descripcion'])),
                            "agregado" => $row['agregado']);
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);
                break;				
			///////////////////////////////////	
			//// AÑADIDO PARA PROMOCIONES /////
			///////////////////////////////////						
			case "cargarRequeridosPromociones":
                $lc_sql = "EXEC [promociones].[Plus_Requeridos_Promociones] ".  "'" . $lc_datos[0] . "'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("plu_id" => $row['plu_id'],
                            "cantidad_plu" => $row['cantidad_plu'],
							"plu_descripcion" => utf8_encode(trim($row['plu_descripcion'])),
							"plu_num_plu" => $row['plu_num_plu'],
                            "agregado" => $row['agregado']);
                    }
                }
				else
				{
				print_r(sqlsrv_errors());
				}
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);
                break;
			///////////////////////////////////	
			//// AÑADIDO PARA PROMOCIONES /////
			///////////////////////////////////					
				case "cargarCanal":
                $lc_sql = "EXEC [promociones].[CargarCanal] ".  "'" . $lc_datos[0] . "'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("variableV" => $row['variableV']);
                    }
                }
				else
				{
				print_r(sqlsrv_errors());
				}
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);
                break;	
			///////////////////////////////////	
			//// AÑADIDO PARA PROMOCIONES /////
			///////////////////////////////////																						
            case "cargarRestaurantesAsignadosDescuentos":
                $lc_sql = "EXEC [Descuentos].[Descuentos]  " . $lc_datos[0] . ", '" . $lc_datos[1] . "', " . $lc_datos[2];
                //echo($lc_sql);
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
                            "rst_id" => $row['rst_id'],
                            "rst_descripcion" => utf8_encode(trim($row['rst_descripcion'])),
                            "fechainicio" => utf8_encode(trim($row['fechainicio'])),
                            "fechafin" => utf8_encode(trim($row['fechafin'])),
                            "agregado" => $row['agregado']);
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);
                break;
            case "cargarCategoriasDescuentos":
                $lc_sql = "EXEC [Descuentos].[Descuentos]  " . $lc_datos[0] . ", " . $lc_datos[1] . ", " . $lc_datos[2];
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
                            "mag_id" => $row['mag_id'],
                            "mag_descripcion" => utf8_encode(trim($row['mag_descripcion'])),
                            "agregado" => $row['agregado']);
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);
                break;
            case "insertarDescuento":
                $lc_sql = "EXEC [Descuentos].[IA_Descuentos] " . $lc_datos['accion'] . ",'" . $lc_datos["idDescuentos"] . "'," . $lc_datos["cdn_id"] . ",'" . $lc_datos["descripcion"] . "'," . $lc_datos["maximo"] . "," . $lc_datos["automatico"] . "," . $lc_datos["valor"] . "," . $lc_datos["minimo"] . "," . $lc_datos["aplica_min_max"] . "," . $lc_datos["apld_id"] . "," . $lc_datos["seguridad"] . ",'" . $lc_datos["IDTipoDescuento"] . "'," . $lc_datos["aplica_cantidad"] . ",'" . $lc_datos["IDUsersPos"] . "'," . $lc_datos["IDStatus"] . ",'" . $lc_datos["plus"] . "','" . $lc_datos["restaurantes"] . "',". $lc_datos["dsct_cupones"];
                if ($this->fn_ejecutarquery($lc_sql)) {
                    $this->lc_regs['Confirmar'] = 1;
                } else {
                    $this->lc_regs['Confirmar'] = 0;
                }
                return $this->lc_regs;
            break;
            case "TipoAplicaDescuento":
                $lc_sql = "EXEC [promociones].[Clasificacion] $lc_datos[0],'$lc_datos[1]'";
                if($this->fn_ejecutarquery($lc_sql)){
                    while ($row=$this->fn_leerarreglo()){
                        $this->lc_regs[] = array(
                            "apld_descripcion" => $row['apld_descripcion']
                        );
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);
            break;
        }
    }


    function fn_cargar_AplicaDescuento($lc_datos) {
        $this->lc_regs = array();
        $this->lc_regs['str'] = 0;
        $this->lc_regs["datos"] = array();
        $lc_sql = "exec Descuentos.USP_Cargar_Aplica_Descuento " . $lc_datos['estado'];
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs["datos"][] = [
                    "apld_id" => $row['apld_id'],
                    "apld_descripcion" => utf8_decode($row['apld_descripcion'])
                ];
            }
        }
        $this->lc_regs['str'] = $this->fn_numregistro();
        return $this->lc_regs;
    }

   	 function fn_cargar_TipoDescuento($lc_datos) {
        $this->lc_regs = array();
        $this->lc_regs['str'] = 0;
        $this->lc_regs["datos"] = array();
        $lc_sql = "exec Descuentos.USP_Cargar_Tipo_Descuento " . $lc_datos['estado'];
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs["datos"][] = [
                    "tpd_id" => $row['IDTipoDescuento'],
                    "tpd_descripcion" => utf8_decode($row['tpd_descripcion'])
                ];
            }
        }
        $this->lc_regs['str'] = $this->fn_numregistro();
        return $this->lc_regs;
    }

    function fn_cargar_Plus_Cadena($cdn) {
        $this->lc_regs = array();
        $lc_query = "exec config.USP_Carga_Select_Plus_Preguntas_Sugeridas $cdn, 'Todo'";
        if ($this->fn_ejecutarquery($lc_query)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array(
                    'plu_num_plu' => $row['plu_num_plu'],
                    'plu_id' => $row['plu_id'],
                    'descripcion' => utf8_encode(trim($row['descripcion'])),
                    'plu_descripcion' => utf8_encode(trim($row['plu_descripcion'])));
            }
            $this->lc_regs['str'] = $this->fn_numregistro();
        }
        return $this->lc_regs;
    }
    
    function fn_validarCondicionesPLU($datos){
        $lc_sql="exec DESCUENTOS.validarCondicionesPLU ".$datos["cdn_id"].",".$datos["plu_id"];
        if ($this->fn_ejecutarquery($lc_sql)) {
            $row = $this->fn_leerarreglo();   
            return utf8_decode($row[0]);
        }
        return "ERROR";
    }
  
    function fn_cargarEstadosTiposDescuento(){
        $lc_sql="select s.IDStatus,std_descripcion from Status s
            join modulo mdl
            on s.mdl_id=mdl.mdl_id
            where mdl_descripcion='Descuentos'";
        $this->fn_ejecutarquery($lc_sql);
        while($row = $this->fn_leerarreglo()){
            $resultado[]=$row;
        }
        return $resultado;
    }
    
    function fn_cargarTiposDescuento($lc_condiciones){  
        $filtroEstado=$lc_condiciones["estado_id"]==0?"":$lc_condiciones["estado_id"];
        $lc_sql = "exec DESCUENTOS.Descuentos_tipo_descuento 0,'','',0,'" . $filtroEstado . "',''";
        $this->fn_ejecutarquery($lc_sql);
        $resultado["str"]=$this->fn_numregistro();
        while($row = $this->fn_leerarreglo()){
            $resultado[]=$row;
        }
        return $resultado;
    }
    
    function fn_guardarTipoDescuentos($lc_condiciones){  
        $accion=$lc_condiciones["IDTipoDescuento"]==0?1:2;
        $lc_sql = "exec DESCUENTOS.Descuentos_tipo_descuento " . $accion . ",'" . $lc_condiciones["IDTipoDescuento"] . "','" . $lc_condiciones["tpd_descripcion"] . "'," . $lc_condiciones["estado"] . ",'','" . $lc_condiciones["IDUsersPos"] . "'";
        if ($this->fn_ejecutarquery($lc_sql)) {
            $this->lc_regs['Confirmar'] = 1;
        } else {
            $this->lc_regs['Confirmar'] = 0;
        }
        return $this->lc_regs;
    }
    
    function fn_cargarAplicaDescuento($lc_condiciones){  
        $filtroEstado=$lc_condiciones["estado_id"]==0?"":$lc_condiciones["estado_id"];
        $lc_sql="exec DESCUENTOS.Descuentos_aplica_descuento 0,'','',0,'".$filtroEstado."'";
        $this->fn_ejecutarquery($lc_sql);
        $resultado["str"]=$this->fn_numregistro();
        while($row = $this->fn_leerarreglo()){
            $resultado[]=$row;
        }
        return $resultado;
    }
    
    function fn_guardarAplicaDescuentos($lc_condiciones){  
        $accion=$lc_condiciones["IDTipoDescuento"]==0?1:2;
        $lc_sql="exec DESCUENTOS.Descuentos_aplica_descuento ".$accion.",'".$lc_condiciones["IDTipoDescuento"]."','".$lc_condiciones["apld_descripcion"]."',".$lc_condiciones["estado"].",''";
        if ($this->fn_ejecutarquery($lc_sql)) {
            $this->lc_regs['Confirmar'] = 1;
        } else {
            $this->lc_regs['Confirmar'] = 0;
        }
        return $this->lc_regs;
    }
}