<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////
////////DESARROLLADO POR: CHRISTIAN PINTO///////////////////////////////////////////////////////////////
///////////DESCRIPCION: CREACION DE CLIENTES  //////////////////////////////////////////////////////////
////////////////TABLAS: Cliente ////////////////////////////////////////////////////////////////////////
////////FECHA CREACION: 23/08/2015//////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////


class AdminClientes extends sql{
	//private $lc_regs;
	//constructor de la clase
	function __construct(){
		parent ::__construct();
	}

    function cargarPoliticaPantallaClientes($cdn_id){
	    $parametros["descripcion"]="CAMPO FORMULARIO CLIENTES";
        $parametros["cdn_id"]=$cdn_id;
	    return $this->consultarColeccionPantallaClientes('cargarpoliticapais',$parametros);
    }

    function cargarCamposConfigurados($cdn_id){
        $parametros["descripcion"]="CAMPO FORMULARIO CLIENTES";
        $parametros["cdn_id"]=$cdn_id;
        return $this->consultarColeccionPantallaClientes('cargarcamposconfigurados',$parametros);
    }
    function cargarcamposTablaCliente($cdn_id){
        $parametros["descripcion"]="CAMPO FORMULARIO CLIENTES";
        $parametros["cdn_id"]=$cdn_id;
        return $this->consultarColeccionPantallaClientes('cargarcamposcliente',$parametros);
    }

    function consultarColeccionPantallaClientes($accion,$parametros){
        $consulta="EXEC [config].[USP_politicasclientes] '".$accion."', '".$parametros["descripcion"]."',".$parametros["cdn_id"];
        $ejecucion=$this->fn_ejecutarquery2($consulta);
        return $ejecucion;
    }

    function insertarColeccionPantallaClientes($parametros){
        $parametros["descripcion"]="CAMPO FORMULARIO CLIENTES";
        return $this->modificarColeccionPantallaClientes('insertarpoliticapais',$parametros);
    }

    function modificarColeccionPantallaClientes($accion,$parametros){
        $consulta = "EXEC [config].[IAE_politicasclientes] '".$accion."', '".$parametros["descripcion"]."', ".$parametros["cdn_id"].", '".$parametros["usr_id"]."'";
        $ejecucion = $this->fn_ejecutarquery2($consulta);
        return $ejecucion;
    }

    function modificarCampos($parametros){
        $parametros["descripcion"]="CAMPO FORMULARIO CLIENTES";
        //Consultar los campos que estan configurados (activos e inactivos)
        $consulta="EXEC [config].[USP_politicasclientes] 'cargarcamposconfigurados', '".$parametros["descripcion"]."',".$parametros["cdn_id"];
        $camposConfigurados=$this->reordenarArray($this->fn_ejecutarquery2($consulta)->datos,"campo");

        //Campos en la bdd que están activos
        $camposConfiguradosActivos = array_filter($camposConfigurados,array($this, 'filtroActivos'));
        //Campos en la bdd que están inactivos
        $camposConfiguradosInactivos = array_filter($camposConfigurados, array($this,"filtroInactivos"));

        //Campos que fueron enviados en la petición
        $camposActivosEnviados=$this->reordenarArray($parametros["valoresactivos"],"campo");
        $camposInactivosEnviados=$parametros["valoresinactivos"];
        //var_dump($camposConfigurados);
        //var_dump($camposConfiguradosActivos);
        //var_dump($camposConfiguradosInactivos);
        //var_dump($camposActivosEnviados);

        $arrayTemporalInactivos=[];
        foreach($camposInactivosEnviados as $campoInactivo){
            $arrayTemporalInactivos[]=$campoInactivo["nombreCampo"];
        }

        $stringInactivos=implode("|",$arrayTemporalInactivos);
        $this->desactivarCampos($stringInactivos, $parametros["cdn_id"], $parametros["usr_id"]);
        //Comparar con los campos que entran

        //campos para actualizar
        $camposActivosActualizables = array_intersect_assoc($camposActivosEnviados, $camposConfiguradosActivos);
        //Activar los campos que se reactivaron
        //Modificar los campos que ya existen
        foreach($camposActivosActualizables as $campoActualizable){
            if($camposConfiguradosActivos[$campoActualizable["campo"]]!==$campoActualizable){
                $this->insertarNuevoCampo($campoActualizable, $parametros["cdn_id"], $parametros["usr_id"]);
            }
        }

        //Encontrar campos para insertar
        $camposNuevos=array_diff_key($camposActivosEnviados,$camposConfiguradosActivos);
        //Insertar los nuevos campos
        foreach($camposNuevos as $nuevoCampo){
            $this->insertarNuevoCampo($nuevoCampo,$parametros["cdn_id"],$parametros["usr_id"]);
        }

    }

    function insertarNuevoCampo($valoresRegistro, $idCadena, $idUsuario){
        $consulta = "EXEC [config].[IAE_parametros_formulario_clientes] 'insertarmodificar',$idCadena,'".$valoresRegistro["campo"]."','".$valoresRegistro["alias"]."',".$valoresRegistro["orden"].", ".$valoresRegistro["obligatorio"].", 1,'$idUsuario'";
        //die($consulta);
        $ejecucion = $this->fn_ejecutarquery2($consulta);
        return $ejecucion;
    }

    function desactivarCampos($stringCampos, $idCadena, $idUsuario){
        $consulta = "EXEC [config].[IAE_parametros_formulario_clientes] 'desactivar',$idCadena,'".$stringCampos."','',0,0, 1,'$idUsuario'";
        $ejecucion = $this->fn_ejecutarquery2($consulta);
        return $ejecucion;
    }

    function filtroActivos($registro){
        return (1==$registro["activo"]);
    }
    function filtroInactivos($registro){
        return (0==$registro["activo"]);
    }

    function reordenarArray($array,$clave){
        $nuevoarray=array();
        foreach($array as $registro){
            $claveActual=trim($registro[$clave]);
            $nuevoarray[$claveActual]=$registro;
        }
        return $nuevoarray;
    }

}

