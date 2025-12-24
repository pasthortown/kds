<?php

/*
FECHA CREACION   : 05/02/2019 
DESARROLLADO POR : Daniel Llerena
DESCRIPCION      : Configuracion de productos Up Selling
*/

class ConfiguracionProductosUpSelling extends sql {
    
    function __construct() {
        parent::__construct();
    }
    
    function cargarProductosConfigurados($accion, $idCadena, $idProducto) {        
        $lc_sql = "EXECUTE [config].[USP_ProductosUPSelling] ".$accion.", ".$idCadena.", ".$idProducto."";
              
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array(
                    "idColeccionPlus"           => $row['idColeccionPlus'] 
                    , "idColeccionDeDatosPlus"  => $row['idColeccionDeDatosPlus'] 
                    , "idProductoBase"          => $row['idProductoBase'] 
                    , "numeroPlu"               => $row['numeroPlu'] 
                    , "producto"                => utf8_encode($row['producto'])
                    , "clasificacion"           => $row['clasificacion']
                    , "masterPlu"               => $row['masterPlu']
                    , "estado"                  => $row['estado']
                );
            }
        }
        
        $this->lc_regs['str'] = $this->fn_numregistro();
        return json_encode($this->lc_regs);
    }
    
    function productos($accion, $idCadena, $idProducto) {
        $lc_sql = "EXECUTE [config].[USP_ProductosUPSelling] ".$accion.", ".$idCadena.", ".$idProducto."";
              
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array(
                    "idProducto"  => $row['idProducto'] 
                    , "producto"  => utf8_encode($row['producto'])
                );
            }
        }
        
        $this->lc_regs['str'] = $this->fn_numregistro();
        return json_encode($this->lc_regs);
    }
    
    function validaColeccionUpSelling($accion, $idCadena, $idProductoBase, $idProductoMejora, $idUsuario) {
        $lc_sql = "EXECUTE [config].[IAE_ProductosUPSelling] ".$accion.", ".$idCadena.", ".$idProductoBase.", '".$idProductoMejora."', '".$idUsuario."'";
              
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array(
                    "existe"  => $row["existe"]                     
                );
            }
        }
        
        $this->lc_regs['str'] = $this->fn_numregistro();
        return json_encode($this->lc_regs);
    }
    
    function guardar($accion, $idCadena, $idProductoBase, $idProductoMejora, $idUsuario) {
        $lc_sql = "EXECUTE [config].[IAE_ProductosUPSelling] '".$accion."', ".$idCadena.", ".$idProductoBase.", '".$idProductoMejora."', '".$idUsuario."'";
        
        $result = $this->fn_ejecutarquery($lc_sql);
        if ($result){ return true; }else{ return false; };
    }
    
    function productosMejora($accion, $idCadena, $idProducto) {
        $lc_sql = "EXECUTE [config].[USP_ProductosUPSelling] ".$accion.", ".$idCadena.", ".$idProducto."";
              
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array(
                    "idProductoBase"        => $row['idProductoBase'] 
                    , "numeroProductoBase"  => $row['numeroProductoBase'] 
                    , "productoBase"        => utf8_encode($row['productoBase']) 
                    , "idProductoMejorado"  => $row['idProductoMejorado']                     
                    , "numeroPluMejora"     => $row['numeroPluMejora']  
                    , "productoMejora"      => utf8_encode($row['productoMejora'])
                );
            }
        }
        
        $this->lc_regs['str'] = $this->fn_numregistro();
        return json_encode($this->lc_regs);   
    }
}
