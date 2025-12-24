<?php
//session_start();
///////////////////////////////////////////////////////////////////////////
////////DESARROLLADO POR JOSE FERNANDEZ////////////////////////////////////
///////////DESCRIPCION: PANTALLA DE FUNCIONES DEL GERENTE//////////////////
////////////////TABLAS: PANTALLA,PERMISOS_PERFIL///////////////////////////
////////FECHA CREACION: 25/03/2014/////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////
class funciones_gerente extends sql {	
        
    function fn_consultapantallaGerente($lc_datos) {
        $lc_query = "EXECUTE [config].[USP_FuncionesGerente_acceso_pantalla] '$lc_datos[0]'";	
        if($this->fn_ejecutarquery($lc_query)) {
            while($row = $this->fn_leerarreglo()) {				
                $this->lc_regs[] = array (
                    "pnt_id"=>$row['pnt_id'],
                    "pnt_Nombre_Mostrar"=>$row['pnt_Nombre_Mostrar'],
                    "pnt_Nombre_Formulario"=>$row['pnt_Nombre_Formulario'],
                    "ruta"=>$row['ruta']
                );
            }
            $this->lc_regs['str']=$this->fn_numregistro();
            return json_encode($this->lc_regs);	
        }
        $this->fn_liberarecurso();    
    }

    function fn_apagar_Estacion($lc_datos){
        $lc_query = "EXEC [facturacion].[IAE_ApagarCaja] $lc_datos[0], '$lc_datos[2]', '$lc_datos[1]'";
        $result = $this->fn_ejecutarquery($lc_query);
        if ($result){ return true; }else{ return false; };
    }

    function fn_obtenerMesa($lc_datos){
        $lc_query = "EXEC pedido.ORD_asignar_mesaordenpedido ".$lc_datos[0].", '".$lc_datos[1]."', '".$lc_datos[2]."'";
        if($this->fn_ejecutarquery($lc_query)){
            while($row = $this->fn_leerarreglo()){
                $this->lc_regs['respuesta'] = $row['respuesta'];
                $this->lc_regs['IDFactura'] = $row['IDFactura'];
                $this->lc_regs['IDOrdenPedido'] = $row['IDOrdenPedido'];
                $this->lc_regs['IDMesa'] = $row['IDMesa'];
            }
        }
        $this->lc_regs['str'] = $this->fn_numregistro();
        return json_encode($this->lc_regs);
    }
    
    function fn_reiniciarImpresion($lc_datos){
        $lc_query = "EXEC [facturacion].[SERVICIO_IAE_ReinciarImpresion] '$lc_datos[0]', '$lc_datos[1]'";
        $result = $this->fn_ejecutarquery($lc_query);
        if ($result){ return true; }else{ return false; };
    }
    
    function configuracionTurnero($idCadena, $idRestaurante) {
        $lc_query = "exec [config].[configuracionTurnero] ".$idCadena.", ".$idRestaurante;
        if($this->fn_ejecutarquery($lc_query)) {
            while($row = $this->fn_leerarreglo()) {
                $this->lc_regs['url'] = $row['url'];
                $this->lc_regs['activo'] = $row['activo'];
            }
        }
        return json_encode($this->lc_regs);
    }
}


