<?php

//////////////////////////////////////////////////////////////
////////DESARROLLADO POR: Worman Andrade//////////////////////
////////DESCRIPCION: Clase para Carga de Combos///////////////
/////////////////////En las Mesas/////////////////////////////
///////TABLAS INVOLUCRADAS: Cadena, Restaurante //////////////
////////////////////////////Pisos, AreaPisos, Mesas///////////
////////////////////////////Reservas//////////////////////////
///////FECHA CREACION: 28-Enero-2014//////////////////////////
//////////////////////////////////////////////////////////////
////////MODIFICADO: Jorge Tinoco /////////////////////////////
////////FECHA: 16-07-2014 ////////////////////////////////////
////////DESCRIPCION: Complementar consultas para imagenes ////
//////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////// 

class mesas extends sql {

    //constructor de la clase
    function __construct() {
        //con herencia 
        parent::__construct();
    }

    var $code = "";

/////////////////////////////////////////CARGAR CADENA///////////////////////////////////////////////////////
    function cargarCadena() {
        $lc_query = "SELECT cdn_id,cdn_descripcion FROM Cadena ORDER BY cdn_descripcion ASC";
        echo $lc_query;
        $lc_datos = $this->fn_ejecutarquery($lc_query);
        $lc_numreg = $this->fn_numregistro();
        if ($lc_numreg > 0) {
            while ($row = $this->fn_leerarreglo()) {
                echo '<option value="' . $row["cdn_id"] . '">' . $row["cdn_descripcion"] . '</option>';
            }
        }
    }

/////////////////////////////////////////CARGAR RESTAURANTE////////////////////////////////////////////////////
    function cargarRestaurante($lc_code) {
        $lc_query = "SELECT rst_id, rst_cod_tienda, rst_descripcion FROM Restaurante WHERE cdn_id = " . $lc_code . " ORDER BY rst_cod_tienda ASC";

        echo $lc_query;

        $lc_datos = $this->fn_ejecutarquery($lc_query);
        $lc_numreg = $this->fn_numregistro();
        if ($lc_numreg > 0) {
            while ($row = $this->fn_leerarreglo()) {
                echo '<option value="' . $row["rst_id"] . '">' . $row["rst_cod_tienda"] . '&nbsp;' . $row["rst_descripcion"] . '</option>';
            }
        }
    }

/////////////////////////////////////////CARGAR PISO DEL RESTAURANTE//////////////////////////////////////////
    function cargarPiso($lc_code) {
        $lc_query = "SELECT pis_id, pis_numero FROM Pisos WHERE rst_id = " . $lc_code;

        echo $lc_query;

        $lc_datos = $this->fn_ejecutarquery($lc_query);
        $lc_numreg = $this->fn_numregistro();
        if ($lc_numreg > 0) {
            while ($row = $this->fn_leerarreglo()) {
                echo '<option value="' . $row["pis_id"] . '">' . $row["pis_numero"] . '</option>';
            }
        } else {
            echo '<option value="1">1</option>';
        }
    }

/////////////////////////////////////////CARGAR AREA DEL RESTAURANTE////////////////////////////////////////
    function cargarArea($lc_code) {
        $lc_query = 'SELECT arp_id, arp_descripcion FROM AreaPiso WHERE pis_id = ' . $lc_code;

        echo $lc_query;

        $lc_datos = $this->fn_ejecutarquery($lc_query);
        $lc_numreg = $this->fn_numregistro();
        if ($lc_numreg > 0) {
            $area = array();
            while ($row = $this->fn_leerarreglo()) {
                echo '<option value="' . $row["arp_id"] . '">' . $row["arp_descripcion"] . '</option>';
            }
        } else {
            echo '<option value="1">Primario</option>';
        }
    }

/////////////////////////////////////////CARGAR COMBOS USUARIO////////////////////////////////////////

    function fn_consultar($lc_opcion, $lc_datos) {
        switch ($lc_opcion) {
            case 'ResfrescarPanelMesas':
                $lc_query = "EXEC [config].[USP_ResfrescarPanelMesas] $lc_datos[0]";
                if ($this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
                            "habilitado" => $row['habilitado'],
                            "tiempo" => $row['tiempo']
                        );
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
            	break;
                
            case 'verificaSeleccionNumeroMesa':
                $lc_query = "EXEC [config].[USP_verifica_seleccionCantidadMesaDisponible] $lc_datos[0]";
                if ($this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
                            "resultado" => $row['resultado']
                        );
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
            	break;
                
            case 'pedidoRapidoEnEspera':
                $lc_query = "EXEC [config].[USP_pedidoRapidoEnEspera] $lc_datos[0], '$lc_datos[1]'";
                if ($this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
                            "mensaje" => $row['mensaje'],
                            "style" => $row['style'],
                            "title" => $row['title'],
                            "mens_alerta" => $row['mens_alerta'],
                        );
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
            	break;
                
            case 'retomaFacturaPendiente':
                $lc_query = "EXEC [config].[valida_factura_pendiente_FS] $lc_datos[0], '$lc_datos[1]'";
                if ($this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
                            "IDCabeceraOrdenPedido" => $row['IDCabeceraOrdenPedido'],
                            "dop_cuenta" => $row['dop_cuenta']
                        );
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
            	break;
                
            case 'estadoMesa':
                $lc_query = " EXEC  [Config].[USP_estadoMesa]  1, '$lc_datos[0]'";
                if ($this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
                            "estado_mesa" => $row['estado_mesa'],
                            "ruta" => $row['ruta'],
                            "ruta_E" => $row['ruta_E'],
                            "mesa_descripcion" => empty($row['mesa_descripcion']) ? '' : $row['mesa_descripcion']
                        );
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
            	break;

            case 'obtiene_url':
                $lc_query = "EXEC  [facturacion].[UPS_ESTADO_ORDEN_Y_FACTURA] '$lc_datos[0]','$lc_datos[1]'";
                if ($this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("url_cuenta" => $row['url_cuenta']);
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
            	break;

            case "cargarPermisos":
                $lc_sql = "EXEC [config].[USP_cargarPermisos] '$lc_datos[0]',$lc_datos[1] ";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
                            "id_acceso" => $row['IDAccesoPos']
                            , "descripcion" => $row['acc_descripcion']
                        );
                    }
                }
                
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);
            	break;

            case 'cargarPiso':
                $lc_query = "EXEC  [config].[UPS_administracionPisosAreas]  1,'$lc_datos[2]','$lc_datos[1]','$lc_datos[0]','' ";
                if ($this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("pis_id" => $row['IDPisos'],
                            "pis_numero" => $row['pis_numero'],
                            "rst_num_personas" => $row['rst_num_personas'],
                            "classCss" => $row['classCss'],
                            "pisoDefecto" => $row['pisoDefecto']
                        );
                    }
                    
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                
                $this->fn_liberarecurso();
            	break;
                
            case 'CargarArea':
                $lc_query = "EXEC  [config].[UPS_administracionPisosAreas]  2,'$lc_datos[2]','$lc_datos[1]','','$lc_datos[0]' ";
                if ($this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("arp_id" => $row['IDAreaPiso'],
                            "arp_descripcion" => $row['arp_descripcion'],
                            "arp_imagen" => utf8_encode($row['arp_imagen']),
                            "classCss" => $row['classCss'],
                            "AreaPisoDefecto" => $row['AreaPisoDefecto']
                        );
                    }

                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                
                $this->fn_liberarecurso();
            	break;

            case 'CargarMesa':
                $lc_query = "EXEC [config].[USP_cargarMesas] '$lc_datos[0]', '$lc_datos[2]', '$lc_datos[3]', '$lc_datos[4]', '$lc_datos[5]' ";
                if ($this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
                            'mesa_id'               => $row['IDMesa']
                            , 'mesa_descripcion'    => $row['mesa_descripcion']
                            , 'mesa_coordenadax'    =>(float) $row['mesa_coordenadax']
                            , 'mesa_coordenaday'    => (float)$row['mesa_coordenaday']
                            , 'std_id'              => $row['IDStatus']
                            , 'std_descripcion'     => $row['std_descripcion']
                            , 'mesa_dimension'      => $row['mesa_dimension']
                            , 'tmes_ruta_imagen'    => $row['tmes_ruta_imagen']
                            , 'mi_mesa'             => $row['mi_mesa']
                            , 'Estacion_asociada'   => $row['Estacion_asociada']
                            , 'total_pedidos'       => $row['total_pedidos']
                            , 'errores'             => (int)$row['errores']
                            , 'nombreCliente'       => utf8_encode($row['nombreCliente'])
                        );
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                
                $this->fn_liberarecurso();
            	break;
                
            case 'VerificarMisMesa':
                $lc_query = "EXEC config.USP_VerificarMisMesa '$lc_datos[0]', '$lc_datos[1]', '$lc_datos[2]', '$lc_datos[3]'";
                if ($this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("mensaje" => $row['mensaje'],
                            "ruta" => $row['ruta'],
                            "ruta_E" => $row['ruta_E'],
                            "estado_mesa" => utf8_encode($row['estado_mesa']),
                            "cat_id" => $row['cat_id'],
                            "odp_id" => $row['odp_id'],
                            "facturaPendiente"=>$row['facturaPendiente'],
                            "usr_usuario" => utf8_encode($row['usr_usuario']),
                            "codigo_app" => $row['codigo_app']
                        );
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                $this->fn_liberarecurso();
            	break;

            case 'CargarEstadoMesas':
                $lc_query = "EXEC [config].[USP_MESA_area] 1, '$lc_datos[0]','$lc_datos[1]'";
                if ($this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("existe" => $row['Resultado']);
                    }
                    
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                
                $this->fn_liberarecurso();
            	break;

            case 'pedidoRapido':
                $lc_query = "EXEC [config].[USP_MESA_area] 2, '$lc_datos[0]','$lc_datos[1]'";
                if ($this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("IDMesa" => $row['IDMesa']);
                    }
                    
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                
                $this->fn_liberarecurso();
            	break;
                
            case 'TransferenciaCuentas':
                $lc_query = "EXEC [config].[USP_Transferencia_Orden_Pedido] $lc_datos[0],'$lc_datos[1]','$lc_datos[2]','$lc_datos[3]',$lc_datos[4]";
                if ($this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
                            "IDCabeceraOrdenPedido" => $row['IDCabeceraOrdenPedido'],
                            "mesa_descripcion"=>$row['mesa_descripcion'],
                            "IDMesa"=>$row['IDMesa'],
                            "IDEstacion"=>$row['IDEstacion'],
                            "IDUsersPos"=>$row['IDUsersPos'],
                            "usr_descripcion"=>$row['usr_descripcion']           
                        );
                    }
                    
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                
                $this->fn_liberarecurso();
            	break;
                
            case 'obtenerUsuarios':
                $lc_query = "EXEC [config].[USP_Transferencia_Orden_Pedido] $lc_datos[0],'$lc_datos[1]','$lc_datos[2]','$lc_datos[3]',$lc_datos[4]";
                if ($this->fn_ejecutarquery($lc_query)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array (
                            "est_nombre" => $row['est_nombre'],
                            "IDEstacion"=>$row['IDEstacion'],
                            "IDUsersPos"=>$row['IDUsersPos']          
                        );
                    }
                    
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                
                $this->fn_liberarecurso();
            	break;   
                
            case 'ActualizaTransferenciaCuentas':
                $lc_query = "EXEC [config].[IAE_Transferencia_Orden_Pedido] '$lc_datos[0]','$lc_datos[1]','$lc_datos[2]','$lc_datos[3]'";
                if ($this->fn_ejecutarquery($lc_query)) {                    
                    while ($row = $this->fn_leerarreglo()) {                        
                        $this->lc_regs[] = array(
                            "mensaje" => $row['mensaje'],         
                        );
                    }
                    
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);                    
                }
                
                $this->fn_liberarecurso();
            	break;  
case 'actualizaTodasOdp':
                $lc_query = "EXEC [seguridad].[IAE_CC_ActualizaCuentasAbieras] $lc_datos[0],'$lc_datos[1]','$lc_datos[2]','$lc_datos[3]','$lc_datos[4]', '$lc_datos[5]', '$lc_datos[6]'";
                if ($this->fn_ejecutarquery($lc_query)) {                    
                    while ($row = $this->fn_leerarreglo()) {                        
                        $this->lc_regs[] = array(
                            "resp" => $row['resp'],
                            "mensaje" => $row['mensaje']         
                        );
                    }
                    
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);                    
                }
                
                $this->fn_liberarecurso();
            	break;  
                
        }
    }
	
	function fidelizacionActiva($idRestaurante) {
		$lc_query = "SELECT [config].[fn_RESTAURANTE_CONFIGURACION_FIDELIZACION_CLIENTES] ($idRestaurante) AS fidelizacionActiva";
		if ($this->fn_ejecutarquery($lc_query)) {
			$row = $this->fn_leerarreglo();
			$this->lc_regs["fidelizacionActiva"] = $row['fidelizacionActiva'];
			return json_encode($this->lc_regs);
		}
	}

}
