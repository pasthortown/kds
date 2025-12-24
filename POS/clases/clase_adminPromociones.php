<?php

/*
FECHA CREACION   : 16/07/2016 
DESARROLLADO POR : Daniel Llerena
DESCRIPCION      : Configuracion de promociones
*/

class configuracionPromociones extends sql
{
    //constructor de la clase
    function __construct() 
    {
        parent ::__construct();
    }
    
    function fn_cargar_Plus_Cadena($cdn) {
        $this->lc_regs = array();
        $lc_query = "exec config.USP_Carga_Select_Plus_Preguntas_Sugeridas $cdn, 'PRODUCTOSCUPONES'";
        if ($this->fn_ejecutarquery($lc_query)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array(
                    'plu_num_plu' => $row['plu_num_plu'],
                    'plu_id' => $row['plu_id'],
                    'descripcion' => utf8_encode(trim($row['descripcion'])),
                    'plu_descripcion' => utf8_encode(trim($row['plu_descripcion']))
                );
            }
            $this->lc_regs['str'] = $this->fn_numregistro();
        }
        return $this->lc_regs;
    }
	
	function fn_cargar_RestauranteP($cdn) {
	$this->lc_regs = array();
	$lc_query = "exec webservices.USP_cargaRestauranteCadena $cdn";
	if ($this->fn_ejecutarquery($lc_query)) {
		while ($row = $this->fn_leerarreglo()) {
			$this->lc_regs[] = array(
				'rst_id' => $row['rst_id'],
				'descripcion' => utf8_encode(trim($row['rst_descripcion'])),
				'descripcion1' => utf8_encode(trim($row['rst_descripcion'])));
		}
		$this->lc_regs['str'] = $this->fn_numregistro();
	}
	return $this->lc_regs;
}
	
	function fn_cargarRestaurante($lc_datos){
        $lc_sql = "EXEC [config].[Promociones_USP_Administracion] ".$lc_datos[0].", ".$lc_datos[1].", ".$lc_datos[2].", ".$lc_datos[3].", '".$lc_datos[4]."'";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array(
                    "IDRestaurante" => $row['IDRestaurante']
                    , "restaurante" => utf8_encode($row['restaurante'])
                );
            }
        }
        $this->lc_regs['str'] = $this->fn_numregistro();
        return json_encode($this->lc_regs);
    }
    
    function fn_cargarPromociones($lc_datos){
        $lc_sql = "EXEC [config].[Promociones_USP_Administracion] ".$lc_datos[0].", ".$lc_datos[1].", ".$lc_datos[2].", ".$lc_datos[3].", '".$lc_datos[4]."'";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array(
                    "IDPromocion" => $row['IDPromocion']
                    , "pro_nombre" => utf8_encode($row['pro_nombre'])
                    , "fechaInicio" => utf8_encode($row['fechaInicio'])
                    , "fechaFin" => utf8_encode($row['fechaFin'])
                    , "contenido" => str_replace(array("\r", "\n"), '<br>', utf8_encode($row['contenido']))
                    , "estado" => $row['estado']
                    , "checkMostrarEtiqueta" => $row['checkMostrarEtiqueta']
                    , "mostrarEtiqueta" => str_replace(array("\r", "\n"), '<br>', utf8_encode($row['mostrarEtiqueta']))
                );
            }
        }
        $this->lc_regs['str'] = $this->fn_numregistro();
        return json_encode($this->lc_regs);  
    }
    
    function fn_guardarPromocion($lc_datos){
        $lc_sql = "EXEC [config].[Promociones_IAE_Administracion] ".$lc_datos[0].", '".$lc_datos[1]."', '".$lc_datos[2]."', '".$lc_datos[3]."', '".$lc_datos[4]."', ".$lc_datos[5].", '".$lc_datos[6]."', '".$lc_datos[7]."', '".$lc_datos[8]."', '".$lc_datos[9]."', ".$lc_datos[10].", '".$lc_datos[11]."', ".$lc_datos[12];
        $result = $this->fn_ejecutarquery($lc_sql);
        if ($result) {
            return true;
        }else{
            return false;
        }
    }
    
    function fn_cargarPromocionRestaurantes($lc_datos){
        $lc_sql = "EXEC [config].[Promociones_USP_Administracion] ".$lc_datos[0].", ".$lc_datos[1].", ".$lc_datos[2].", ".$lc_datos[3].", '".$lc_datos[4]."'";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array(
                    "IDRestaurante" => $row['IDRestaurante']
                    , "restaurante" => utf8_encode($row['restaurante'])
                );
            }
        }
        $this->lc_regs['str'] = $this->fn_numregistro();
        return json_encode($this->lc_regs);
    } 
	
	function fn_consultarListaDescuentosP($lc_regs){
		$this->lc_regs = array();
        //@resultado INT=0, @dsct_id VARCHAR(40)='', @cdn_id INT=0
        $lc_sql = "EXEC [Descuentos].[Descuentos]  12, 'Validos', $lc_regs";
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
}


