<?php

class Domicilio extends sql {

    function cargarTurnosMotorizado( $idPeriodo ) {
        $this->lc_regs = [];
        $lc_sql = "EXEC dbo.DOMICILIO_cargar_turnos_cerrados_motorizados '$idPeriodo'";
        try {
            $this->fn_ejecutarquery( $lc_sql );
            if ( $this->fn_numregistro() > 0 ) {
                while ($row = $this->fn_leerarreglo()) {
                    $this->lc_regs[] = array( "idPeriodoMotorolo" => $row['idPeriodoMotorolo'],
                                              "idMotorizado" => $row['idMotorizado'],
                                              "motorizado" => utf8_encode($row['motorizado']),
                                              "documento" => $row['documento'],
                                              "tipo" => $row['tipo'],
                                              "empresa" => utf8_encode($row['empresa']),
                                              "telefono" => $row['telefono'],
                                              "fecha_inicio" => $row['fecha_inicio'],
                                              "hora_inicio" => $row['hora_inicio'],
                                              "fecha_fin" => $row['fecha_fin'],
                                              "hora_fin" => $row['hora_fin'] );
                }
                $this->lc_regs['registros'] = $this->fn_numregistro();
            }
        } catch (Exception $e) {
            return $e;
        }
        return $this->lc_regs;
    }

    function cargarMotorizadosActivos( $idPeriodo ) {
        $this->lc_regs = [];
        $lc_sql = "EXEC dbo.App_cargar_motorizado_activo '$idPeriodo'";
        try {
            $this->fn_ejecutarquery( $lc_sql );
            if ( $this->fn_numregistro() > 0 ) {
                while ($row = $this->fn_leerarreglo()) {
                    $this->lc_regs[] = array( "idMotorizado" => $row['idMotorizado'],
                                              "motorizado" => utf8_encode($row['motorizado']),
                                              "documento" => $row['documento'],
                                              "tipo" => $row['tipo'],
                                              "empresa" => utf8_encode($row['empresa']),
                                              "telefono" => $row['telefono'] );
                }
                $this->lc_regs['registros'] = $this->fn_numregistro();
            }
        } catch (Exception $e) {
            return $e;
        }
        return $this->lc_regs;
    }

    function cargarMotorizadosAsignadosPeriodo( $idCadena, $idRestaurante, $idPeriodo ) {
        $this->lc_regs = [];
        $lc_sql = "EXEC dbo.App_cargar_motorizado_periodo $idCadena, $idRestaurante, '$idPeriodo'";
        try {
            $this->fn_ejecutarquery( $lc_sql );
            if ( $this->fn_numregistro() > 0 ) {
                while ($row = $this->fn_leerarreglo()) {
                    $this->lc_regs[] = array( "idMotorizado" => $row['idMotorizado'],
                                              "motorizado" => utf8_encode($row['motorizado']),
                                              "documento" => $row['documento'],
                                              "tipo" => $row['tipo'],
                                              "empresa" => $row['empresa'],
                                              "total" => $row['total'],
                                              "maximo_ordenes" => $row['maximo_ordenes'],
                                              "fecha_inicio" => $row['fecha_inicio'],
                                              "hora_inicio" => $row['hora_inicio'],
                                              "fecha_fin" => $row['fecha_fin'],
                                              "hora_fin" => $row['hora_fin'],
                                              "telefono" => $row['telefono'],
                                              "estado" => $row['estado'] );
                }
                $this->lc_regs['registros'] = $this->fn_numregistro();
            }
        } catch (Exception $e) {
            return $e;
        }
        return $this->lc_regs;
    }

    function cargarTransaccionesAsignadasPorMotorizado( $idCadena, $idRestaurante, $idMotorizado ) {
        $this->lc_regs = [];
        $lc_sql = "EXEC dbo.App_cargar_pedidos_motorolo $idCadena, $idRestaurante, '$idMotorizado'";
        try {
            $this->fn_ejecutarquery( $lc_sql );
            if ( $this->fn_numregistro() > 0 ) {
                while ($row = $this->fn_leerarreglo()) {
                    $this->lc_regs[] = array( "codigo_app" => $row['codigo_app'],
                                              "codigo" => $row['codigo'],
                                              "telefono" => $row['telefono'],
                                              "cliente" => utf8_encode($row['cliente']),
                                              "total" => $row['total'],
                                              "forma_pago" => $row['forma_pago'] );
                }
                $this->lc_regs['registros'] = $this->fn_numregistro();
            }
        } catch (Exception $e) {
            return $e;
        }
        return $this->lc_regs;
    }

    function finalizarTurnoMotorizado( $idCadena, $idRestaurante, $idPeriodo, $idUsuario, $idMotorizado ) {
        $this->lc_regs = [];
        $query = "EXEC dbo.DOMICILIO_finalizar_turno_motorizado $idCadena, $idRestaurante, '$idPeriodo', '$idUsuario', '$idMotorizado'";
        try {
            $this->fn_ejecutarquery( $query );
            if ( $this->fn_numregistro() > 0 ) {
                $row = $this->fn_leerarreglo();
                $this->lc_regs = array( "mensaje" => $row['mensaje'],
                                        "estado" => $row['estado'] );
            }
        } catch (Exception $e) {
            return $e;
        }
        return $this->lc_regs;
    }

    function asignarTurnoMotorizado( $idCadena, $idRestaurante, $idPeriodo, $idUsuario, $idMotorizado ) {
        $this->lc_regs = [];
        $query = "EXEC dbo.DOMICILIO_asignar_turno_motorizado $idCadena, $idRestaurante, '$idPeriodo', '$idUsuario', '$idMotorizado'";
        try {
            $this->fn_ejecutarquery( $query );
            if ( $this->fn_numregistro() > 0 ) {
                $row = $this->fn_leerarreglo();
                $this->lc_regs = array( "mensaje" => $row['mensaje'],
                                        "estado" => $row['estado'] );
            }
        } catch (Exception $e) {
            return $e;
        }
        return $this->lc_regs;
    }

    function cargarPeriodoAbierto( $idCadena, $idRestaurante ) {
        $this->lc_regs = [];
        $query = "EXEC dbo.DOMICILIO_cargar_periodo_abierto $idCadena, $idRestaurante";
        try {
            $this->fn_ejecutarquery( $query );
            if ( $this->fn_numregistro() > 0 ) {
                $row = $this->fn_leerarreglo();
                $this->lc_regs = array( "idPeriodo" => $row['idPeriodo'],
                                        "idUsuario" => $row['idUsuario'],
                                        "usuario" => utf8_encode($row['usuario']),
                                        "fecha" => $row['fecha'],
                                        "hora" => $row['hora'] );
            }
        } catch (Exception $e) {
            return $e;
        }
        return $this->lc_regs;
    }

    function cambiarEstadoPedidoAEnCamino( $idPeriodo, $idMotorizado, $idUsuario ) {
        $this->lc_regs = [];
        $query = "EXEC dbo.App_cambiar_pedido_en_camino '$idPeriodo', '$idMotorizado', '$idUsuario'";
        try {
            $this->fn_ejecutarquery( $query );
            if ( $this->fn_numregistro() > 0 ) {
                $row = $this->fn_leerarreglo();
                $this->lc_regs = array( "mensaje" => $row['mensaje'],
                                        "estado" => $row['estado'] );
            }
        } catch (Exception $e) {
            return $e;
        }
        return $this->lc_regs;
    }

    function cambiarEstadoPedidoAEntregado( $idPeriodo, $idMotorizado, $idUsuario ) {
        $this->lc_regs = [];
        $query = "EXEC dbo.App_cambiar_pedido_entregado '$idPeriodo', '$idMotorizado', '$idUsuario'";
        try {
            $this->fn_ejecutarquery( $query );
            if ( $this->fn_numregistro() > 0 ) {
                $row = $this->fn_leerarreglo();
                $this->lc_regs = array( "mensaje" => $row['mensaje'],
                                        "estado" => $row['estado'] );
            }
        } catch (Exception $e) {
            return $e;
        }
        return $this->lc_regs;
    }

    function cargarUrlApiMotorizados($idRestaurante) {
        $this->lc_regs = array();
        $lc_sql = "EXEC [config].[USP_Retorna_Direccion_Webservice] '$idRestaurante', 'MOTORIZADO', 'MASTER DATA', 0 ";        

        try {
            $this->fn_ejecutarquery($lc_sql);
            if ( $this->fn_numregistro() > 0 ) {
                $row = $this->fn_leerarreglo();
                $this->lc_regs = array( "url" => $row['direccionws'] );
            }
        } catch (Exception $e) {
            return json_encode($e);
        }
        return $this->lc_regs;
    }

    function cargarMotorizadosActivosAPI($url, $parametro){
        $respuesta = array();
            try {
                //Busqueda en Servicio Centralizado
                    $urlServicioWeb = $url;
        
                    $url = $urlServicioWeb . '/lista';
                    $ch = curl_init($url);
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: application/json'));
                    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
                    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, 
                    json_encode(array(
                            "parametro" => $parametro,
                            "estado"    => 'ACTIVO',
                            "ciudad"    => ''
                        )
                    ));                    
                    //execute get
                    $result = curl_exec($ch);
                    //close connection
                    curl_close($ch);
                    $respuesta = json_decode($result);
                    //Existe Registro
                
                return $respuesta;
            } catch (Exception $e) {
                print json_encode($e);
            }
     
    }


    function notificacionMotorizadoGerente($url, $idPeriodo, $idMotorizado){

        $this->lc_regs = array();
        $lc_sql = "EXEC [dbo].[App_resumen_motorizado_por_periodo]  '$idMotorizado', '$idPeriodo' "; 
        
        try {
            $this->fn_ejecutarquery($lc_sql);
            if ( $this->fn_numregistro() > 0 ) {
                $row = $this->fn_leerarreglo();

                if($row['tipo_motorolo'] == 'INTERNO') {

                    $respuestaInsercion = json_decode($this->notificarComandasMotoroloApi($url,  
                         $row['codigo_restaurante'], 
                         $row['cedula_motorizado'],
                         $row['cantidad_pedidos'], 
                         $row['fecha_maxima_periodo'],
                         $row['nombre_motorizado'],
                         $row['valor_total']));

                    if($respuestaInsercion->status == 200){
                        $this->lc_regs = array( "respuesta" => 1 );

                    }else if($respuestaInsercion->status == 202){

                        return $this->actualizarNotificacionComandasMotoroloApi($url, 
                                                                        $row['codigo_restaurante'], 
                                                                        $row['cedula_motorizado'], 
                                                                        $row['cantidad_pedidos'], 
                                                                        $row['nombre_motorizado'], 
                                                                        $row['valor_total']);

                    }else{
                        $this->lc_regs = array( "respuesta" => 0 );

                    }

                }else{
                    $this->lc_regs = array( "respuesta" => 0 );
                }
            }
        } catch (Exception $e) {
            return json_encode($e);
        }
        return $this->lc_regs;


    }


    function cargarUrlApiMotorizadosGerente($idRestaurante) {
        $this->lc_regs = array();
        $lc_sql = "EXEC [config].[USP_Retorna_Direccion_Webservice] '$idRestaurante', 'GERENTE', 'MOTOROLO COMANDAS', 0 ";      
                
        try {
            $this->fn_ejecutarquery($lc_sql);
            if ( $this->fn_numregistro() > 0 ) {
                $row = $this->fn_leerarreglo();
                $this->lc_regs = array( "url" => $row['direccionws'] );
            }
        } catch (Exception $e) {
            return json_encode($e);
        }
        return $this->lc_regs;
    }


    function notificarComandasMotoroloApi($url, $Cod_Restaurante, $Cedula, $Numero_Comandas, $Fecha, $Nombre, $Precio_Promedio){

        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => $url,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 5,
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_POSTFIELDS => json_encode(array(
            "Cod_Restaurante"   => $Cod_Restaurante,
            "Cedula"            => $Cedula,
            "Numero_Comandas"   => $Numero_Comandas,
            "Fecha"             => $Fecha->format('Y-m-d'),
            "Estado"            => 1,
            "Nombre"            => $Nombre,
            "Tipo_Comanda"      => "NORMAL",
            "Precio"            => $Precio_Promedio,
            "Sistema"           => "MAXPOINT"
            )),
            CURLOPT_HTTPHEADER => array(
            "Content-Type: application/json"
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);

        return  $response;

 }


 function actualizarNotificacionComandasMotoroloApi($url, $Cod_Restaurante, $Cedula, $Numero_Comandas, $Nombre, $Precio_Promedio){

    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => $url,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 5,
      CURLOPT_CUSTOMREQUEST => "PUT",
      CURLOPT_POSTFIELDS => json_encode(array(
        "Cod_Restaurante"   => $Cod_Restaurante,
        "Cedula"            => $Cedula,
        "Numero_Comandas"   => $Numero_Comandas,
        "Nombre"            => $Nombre,
        "Tipo_Comanda"      => "NORMAL",
        "Precio"            => $Precio_Promedio,
        "Sistema"           => "MAXPOINT"
        )),
        CURLOPT_HTTPHEADER => array(
        "Content-Type: application/json"
        ),
    ));

    $response = curl_exec($curl);
    curl_close($curl);

    return $response;

}

function imprimirFinTurnoMotorizado($idPeriodo, $idMotorizado,$idRestaurante, $idUsuario ) {
    $this->lc_regs = array();
    $lc_sql = "EXEC [dbo].[App_ImpresionDesasignacionMotorizado] '$idMotorizado', '$idPeriodo', $idRestaurante, '$idUsuario' ";    
    try {

        $this->fn_ejecutarquery($lc_sql);

        if ( $this->fn_numregistro() > 0 ) {
            $row = $this->fn_leerarreglo();
            $this->lc_regs = array( "confirmar" => $row['confirmar'] );
        }
    } catch (Exception $e) {
        return json_encode($e);
    }
    return $this->lc_regs;
}

function cargarPedidosEntregados( $idCadena, $idRestaurante, $idPeriodo, $estado, $parametroBusqueda ){

    $this->lc_regs = [];
    $lc_sql = "EXEC dbo.App_cargar_pedidos_entregados $idCadena, $idRestaurante, '$idPeriodo', '$estado', '$parametroBusqueda'";
    try {
        $this->fn_ejecutarquery($lc_sql);
        while ($row = $this->fn_leerarreglo()) {
            $this->lc_regs[] = array( "codigo_app" => $row['codigo_app'],
                                      "forma_pago" => $row['forma_pago'],
                                      "codigo" => $row['codigo'],
                                      "documento" => $row['documento'],
                                      "cliente" => utf8_encode($row['cliente']),
                                      "fecha" => $row['fecha'],
                                      "total" => $row['total'],
                                      "telefono" => $row['telefono'],
                                      "direccion_factura" => utf8_encode($row['direccion_factura']),
                                      "email" => utf8_encode($row['email']),
                                      "direccion_despacho" => utf8_encode($row['direccion_despacho']),
                                      "datos_envio" => utf8_encode($row['datos_envio']),
                                      "observacion" => utf8_encode($row['observacion']),
                                      "estado" => $row['estado'],
                                      "idMotorizado" => $row['idMotorolo'],
                                      "motorizado" => utf8_encode($row['motorolo']),
                                      "motorizado_telefono" => $row['motorolo_telefono'],
                                      "tiempo" => $row['tiempo'],
                                      "medio" => $row['medio'],
                                      "color_fila" => $row['color_fila'],
                                      "color_texto" => $row['color_texto'],
                                      "audio" => $row['audio'],
                                      "cambio_estado" => $row['cambio_estado'],
                                      "codigo_externo" => $row['codigo_externo'],
                                      "codigo_factura" => $row['codigo_factura']);
        }
        $this->lc_regs['registros'] = $this->fn_numregistro();
    } catch (Exception $e) {
        print('EXCEPCION');
        print($e);
        return $e;
    }
    return $this->lc_regs;
}

function cargarMotorizados($idCadena,$idRestaurante, $idPeriodo){

    $this->lc_regs = [];
    $lc_sql = "EXEC dbo.App_cargar_motorizado_interno_externo_periodo '$idCadena','$idRestaurante', '$idPeriodo'";

    //var_dump($lc_sql);
    try {
        $this->fn_ejecutarquery($lc_sql);
        while ($row = $this->fn_leerarreglo()) {
            $this->lc_regs[] = array( "idMotorizado" => $row['idMotorizado'],
                                      "motorizado" => utf8_encode($row['motorizado']),
                                      "documento" => $row['documento'],
                                      "tipo" => $row['tipo'],
                                      "empresa" => utf8_encode($row['empresa']),
                                      "total" => $row['total'],
                                      "maximo_ordenes" => $row['maximo_ordenes'],
                                      "estado" => $row['estado'],
                                     );
        }
        $this->lc_regs['registros'] = $this->fn_numregistro();
    } catch (Exception $e) {
        return $e;
    }
    return $this->lc_regs;
}

function asignarMotorizado( $idMotorizado, $codigo_app, $idUsuario ){
    $this->lc_regs = [];
    $lc_sql = "EXEC dbo.App_asignar_pedido_motorolo_admin '$idMotorizado','$codigo_app', '$idUsuario'";
    //var_dump($lc_sql);
    try {
        $this->fn_ejecutarquery($lc_sql);
        while ($row = $this->fn_leerarreglo()) {
            $this->lc_regs[] = array( "mensaje" => utf8_encode($row['mensaje']),
                                      "estado" => $row['estado']);
        }
        $this->lc_regs['registros'] = $this->fn_numregistro();

        $this->insertaAuditoria('adminPedidosDomicilio.php','REASIGNACION DE MORORIZADO','ENTREGADO','Se reasigna el pedido '.$codigo_app.' al motorizado '.$idMotorizado.' por el usuario '.$idUsuario);

    } catch (Exception $e) {
        return $e;
    }
    return $this->lc_regs;
}

public function insertaAuditoria($url, $peticion, $estado, $mensaje)
    {
        $query = "INSERT INTO dbo.Auditoria_EstadosApp
            ( url, peticion, estado, mensaje, fecha )
        VALUES  
            ( '$url', '$peticion', '$estado', '$mensaje', GETDATE())";

        $result = $this->fn_ejecutarquery($query);
        if ($result){ return true; }else{ return false; };
    }

function cargarConfiguracionDomicilio($cdn_id, $rst_id, $usr_id) {
    $this->lc_regs = [];
    $query = "EXEC config.ConfiguracionDomicilio $cdn_id, $rst_id, '$usr_id'";
    try {
        $this->fn_ejecutarquery( $query );
        if ( $this->fn_numregistro() > 0 ) {
            $row = $this->fn_leerarreglo();
            $this->lc_regs = array( "existeCajeroDomicilio" => $row['existeCajeroDomicilio'],
                                    "aplicaDomicilio" => $row['aplicaDomicilio'] );
        }
    } catch (Exception $e) {
        return $e;
    }
    return $this->lc_regs;
}
function agregadores (){
    $this->lc_regs = [];
    $query = "SELECT * FROM [config].[fn_listaAgregadores]()";
    try {
        $this->fn_ejecutarquery( $query );
        while ($row = $this->fn_leerarreglo()) {
            $this->lc_regs[] = array( "id" => $row['id'],
            "descripcion" => $row['descripcion'] );
}
        $this->lc_regs['registros'] = $this->fn_numregistro();
            
    } catch (Exception $e) {
        return $e;
    }
    return $this->lc_regs;
}


}
 

