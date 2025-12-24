<?php

///////////////////////////////////////////////////////////////////////////
////////DESARROLLADO: Juan Estévez ////////////////////////////////////////
////////DESCRIPCION: Creación de EXEC para la cargar y actualizar//////////
/////////////////// la imagenes de pisos y área ///////////////////////////
////////FECHA CREACION: 20 - 12 - 2016  ///////////////////////////////////
///////FECHA ULTIMA MODIFICACION://////////////////////////////////////////
///////USUARIO QUE MODIFICO:///////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////

include_once ("../../system/conexion/clase_sql.php");
class imagen_piso {
    
    public function fn_consultar($lc_opcion, $lc_datos) {
        $lc_config  = new sql();
        switch ($lc_opcion) {
            case 'AreaSelect':
            $lc_query = "EXECUTE config.IAE_imagen_mesa '".$lc_datos[0]."','".$lc_datos[1]."','".$lc_datos[2]."'";
            $lc_datos = $lc_config->fn_ejecutarquery($lc_query);
            if ($lc_datos){
                while($row = $lc_config->fn_leerarreglo()) {
                    $imagen = $row['arp_imagen'];
                }

                $lc_regs['imagen'] = $imagen;
                return json_encode($lc_regs);
            }
            $lc_config->fn_liberarecurso();
            break;

            case 'AreaUpdate':
            $lc_query = "EXECUTE config.IAE_imagen_mesa '".$lc_datos[0]."','".$lc_datos[1]."','".$lc_datos[2]."'";
            $lc_datos = $lc_config->fn_ejecutarquery($lc_query);
            if($lc_datos){    
                $lc_regs['Confirmar'] = 1;
            }else{
                $lc_regs['Confirmar'] = 0;
            }
            return json_encode($lc_regs);
            $lc_config->fn_liberarecurso();
            break;
        }   
    }
}          
