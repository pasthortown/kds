<?php

class Motorizado extends sql {

    function cargarTiposMotorizados( $idCadena ) {
        $this->lc_regs = [];
        $lc_sql = "EXEC dbo.MOT_cargar_tipos_motorizados $idCadena";
        try {
            $this->fn_ejecutarquery( $lc_sql );
            if ($this->fn_numregistro() > 0) {
                while ( $row = $this->fn_leerarreglo() ) {
                    $this->lc_regs[] = array( "tipos" => $row['tipos'] );
                }
                $this->lc_regs['registros'] = $this->fn_numregistro();
            }
        } catch (Exception $e) {
            return $e;
        }
        return $this->lc_regs;
    }

    function cargarMotorizadosPorEstado( $idCadena, $idRestaurante, $estado ) {
        $this->lc_regs = [];
        $lc_sql = "EXEC dbo.MOT_cargar_motorizados_estado $idCadena, $idRestaurante, '$estado'";
        try {
            $this->fn_ejecutarquery( $lc_sql );
            if ($this->fn_numregistro() > 0) {
                while ($row = $this->fn_leerarreglo()) {
                    $this->lc_regs[] = array( "idMotorolo" => $row['idMotorolo'],
                                                "tipo" => $row['tipo'],
                                                "empresa" => $row['empresa'],
                                                "documento" => $row['documento'],
                                                "nombres" => $row['nombres'],
                                                "apellidos" => $row['apellidos'],
                                                "codigo" => $row['codigo'],
                                                "nomina" => $row['nomina'],
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

    function guardarMotorizado( $idMotorolo, $estado, $tipo, $empresa, $documento, $nombres, $apellidos, $telefono, $idTipoIdentificacion, $nomina, $idciudad, $urlApi, $nombreTipoIdentificacion, $nombreCiudad ) {
        $this->lc_regs = [];
        if ( empty( $idMotorolo ) ) {
            $lc_sql = "EXEC dbo.App_IA_motorolo NULL, '$tipo', '$empresa', '$documento', '$nombres', '$apellidos', '$idTipoIdentificacion', '$nomina', '$telefono', '$estado', $idciudad ";
        } else {
            $lc_sql = "EXEC dbo.App_IA_motorolo '$idMotorolo', '$tipo', '$empresa', '$documento', '$nombres', '$apellidos', '$idTipoIdentificacion', '$nomina', '$telefono', '$estado', $idciudad";
        }

        try {
            $this->fn_ejecutarquery( $lc_sql );
            if ( $this->fn_numregistro() > 0 ) {
                $row = $this->fn_leerarreglo();
                $this->lc_regs = array(
                    "estado" => $row['estado'],
                    "mensaje" => $row['mensaje'],
                    "IDMotorolo" => $row['IDMotorolo']
                );

                //Guardado en API
                $this->guardarMotorizadoApi($urlApi, $documento, $nombreTipoIdentificacion, $tipo, $empresa, $nombres, $apellidos, $nomina, $telefono, $estado, $nombreCiudad);

                $this->guardarMotorizadoApiClientes($urlApi, $documento, $nombreTipoIdentificacion, $tipo, $empresa, $nombres, $apellidos, $nomina, $telefono, $estado, $nombreCiudad);

            }
        } catch (Exception $e) {
            return $e;
        }
        return $this->lc_regs;
    }

    function guardarMotorizadoApi($url, $identificacion, $tipoIdentificacion, $tipoMotorolo, $empresaMotorolo, $nombres, $apellidos, $codigoNomina, $telefono, $status, $ciudad){

        $urlServicioWeb = $url;

        $entidad = '{
            "identificacion":"'.$identificacion.'",
            "tipoIdentificacion":"'.$tipoIdentificacion.'",
            "tipoMotorolo":"'.$tipoMotorolo.'",
            "empresaMotorolo":"'.$empresaMotorolo.'",
            "nombres":"'.$nombres.'",
            "apellidos":"'.$apellidos.'",
            "codigoTarjeta":"",
            "codigoNomina":"'.$codigoNomina.'",
            "telefono":"'.$telefono.'",
            "estado":"'.$status.'",
            "ciudad":"'.$ciudad.'"
        }';

        $url = $urlServicioWeb.'/guardar';
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: application/json'));
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $entidad );
        //execute post
        $result = curl_exec($ch);

        //close connection
        curl_close($ch);
        $respuestaSolicitud = json_decode($result);

    }

    function guardarMotorizadoApiClientes($url, $identificacion, $tipoIdentificacion, $tipoMotorolo, $empresaMotorolo, $nombres, $apellidos, $codigoNomina, $telefono, $status, $ciudad){

      $idCadena = $_SESSION['cadenaId'];
      $codigo_tarjeta= '';
      $sistemaOrigenAPI="MAXPOINT";

        $token = $this->validarTokenApiCliente();

        $idClienteMongo =$this->consultarClienteApi($identificacion);
        //var_dump($identificacion);
        $estadoSucces = $idClienteMongo['success'];
        $estadoCode = $idClienteMongo['statusCode'];
        //$datosMotorolos = $idClienteMongo['data']['dataMotorolo'];
        //var_dump($datosMotorolos);
         try {
          $sql_url_base = "SELECT [config].[fn_ColeccionCadena_VariableV] ('".$idCadena."', 'MOTOROLO APIMDMCLIENTE', 'URL API MDM') AS url;";
          $this->fn_ejecutarquery($sql_url_base);
          $arreglo_url_base = $this->fn_leerarreglo();
          $urlAPIMDM = $arreglo_url_base['url'];
          $method = 'POST';
          $hora_unix = time();
          $fechaActualizacion = date('Y-m-d\TH:i:s\Z', $hora_unix);
          if ($identificacion) {
              $method = 'PUT';
          }
        if (($estadoSucces === false) && ($estadoCode === 404) || ($estadoCode === 200)) {
        //Data cliente
        $dataCliente = [
          'cdn_id' => $idCadena,
          'pais' => $ciudad,
          'sistemaOrigen' => $sistemaOrigenAPI,
          'documento' => $identificacion,
          'tipoDocumento' => $tipoIdentificacion,
          'email' => "",
          'telefono' => $telefono,
          'primerNombre' => $nombres,
          'apellidos' => $apellidos,
          'fechaAceptoPrivacidad' =>"1900-01-01T00:00:00Z",
          'aceptacionPoliticas' => false,
          'autenticacion' => false,
          'envioComunicacionesComerciales' => false,
          'envioComunicacionesComercialesPush' => false,
          'analisisDeDatosPerfiles' => false,
          'cesionDatosATercerosNacionales' => false,
          'cesionDatosATercerosInternacionales' =>false

      ];
        // Iniciar cURL
        $curlCliente = curl_init();
        //Opciones cURL
        $optionsCliente = array(
             CURLOPT_URL => $urlAPIMDM . '/api/client/',
             CURLOPT_CUSTOMREQUEST => "POST",
             CURLOPT_RETURNTRANSFER => true,
             CURLOPT_TIMEOUT => 5,
             CURLOPT_CONNECTTIMEOUT => 5,
             CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Accept: application/json',
                'Authorization: Bearer ' . $token['token']
            ),
             CURLOPT_POSTFIELDS => json_encode($dataCliente)
        );
        curl_setopt_array($curlCliente, $optionsCliente);

        $responseCliente = curl_exec($curlCliente);
        //Cerrar Endpoint Motorolo
        curl_close($curlCliente);
        $respuestaSolicitudCliente = json_decode($responseCliente, true);
        //var_dump($vartemporal);

      }
      if(($estadoSucces === true) && ($estadoCode === 200)){
        $data = [
        //  '_id' => $idClienteMongo['data']['cliente']['_id'],
          'cdn_id'=>$idCadena,
          'identificacion' => $identificacion,
          'tipo_motorolo' => $tipoMotorolo,
          'empresa_motorolo' => $empresaMotorolo,
          'telefono' => $telefono,
          'codigo_tarjeta' => $codigo_tarjeta,
          'codigo_nomina' => $codigoNomina,
          'status' => intval($status),
          'ciudad' => $ciudad,
          'fechaActualizacion' => $fechaActualizacion

      ];
      // Iniciar cURL
      $curl = curl_init();
      //Opciones cURL
      $options = array(
           CURLOPT_URL => $urlAPIMDM . '/api/motorolo/',
           CURLOPT_CUSTOMREQUEST => "POST",
           CURLOPT_RETURNTRANSFER => true,
           CURLOPT_TIMEOUT => 5,
           CURLOPT_CONNECTTIMEOUT => 5,
           CURLOPT_HTTPHEADER => array(
              'Content-Type: application/json',
              'Accept: application/json',
              'Authorization: Bearer ' . $token['token']
          ),
           CURLOPT_POSTFIELDS => json_encode($data)
      );
      curl_setopt_array($curl, $options);
      $response = curl_exec($curl);
      //Cerrar Endpoint Motorolo
      curl_close($curl);
        $respuestaSolicitud = json_decode($response, true);
        //var_dump($method);
        //var_dump($respuestaSolicitud);

    }
    if(($estadoSucces === false) && ($estadoCode === 404) || ($estadoCode === 200)){
      $data = [
      //  '_id' => $idClienteMongo['data']['cliente']['_id'],
        'cdn_id'=>$idCadena,
        'identificacion' => $identificacion,
        'tipo_motorolo' => $tipoMotorolo,
        'empresa_motorolo' => $empresaMotorolo,
        'telefono' => $telefono,
        'codigo_tarjeta' => $codigo_tarjeta,
        'codigo_nomina' => $codigoNomina,
        'status' => intval($status),
        'ciudad' => $ciudad,
        'fechaActualizacion' => $fechaActualizacion

    ];
    // Iniciar cURL
    $curl = curl_init();
    //Opciones cURL
    $options = array(
         CURLOPT_URL => $urlAPIMDM . '/api/motorolo/',
         CURLOPT_CUSTOMREQUEST => "POST",
         CURLOPT_RETURNTRANSFER => true,
         CURLOPT_TIMEOUT => 5,
         CURLOPT_CONNECTTIMEOUT => 5,
         CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'Accept: application/json',
            'Authorization: Bearer ' . $token['token']
        ),
         CURLOPT_POSTFIELDS => json_encode($data)
    );
    curl_setopt_array($curl, $options);
    $response = curl_exec($curl);
    //Cerrar Endpoint Motorolo
    curl_close($curl);
      $respuestaSolicitud = json_decode($response, true);
      //var_dump($method);
      //var_dump($respuestaSolicitud);

  }

    if(($estadoSucces === true) && ($estadoCode === 200) ){
      $data = [
        '_id' => $idClienteMongo['data']['dataMotorolo']['_id'],
        'cdn_id'=>$idCadena,
        'identificacion' => $identificacion,
        'tipo_motorolo' => $tipoMotorolo,
        'empresa_motorolo' => $empresaMotorolo,
        'telefono' => $telefono,
        'codigo_tarjeta' => $codigo_tarjeta,
        'codigo_nomina' => $codigoNomina,
        'status' => intval($status),
        'ciudad' => $ciudad,
        'fechaActualizacion' => $fechaActualizacion

    ];
    // Iniciar cURL
    $curl = curl_init();
    //Opciones cURL
    $options = array(
         CURLOPT_URL => $urlAPIMDM . '/api/motorolo/',
         CURLOPT_CUSTOMREQUEST => "PUT",
         CURLOPT_RETURNTRANSFER => true,
         CURLOPT_TIMEOUT => 5,
         CURLOPT_CONNECTTIMEOUT => 5,
         CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'Accept: application/json',
            'Authorization: Bearer ' . $token['token']
        ),
         CURLOPT_POSTFIELDS => json_encode($data)
    );
    curl_setopt_array($curl, $options);
    $response = curl_exec($curl);
    //Cerrar Endpoint Motorolo
    curl_close($curl);
      $respuestaSolicitud = json_decode($response, true);
      //var_dump($method);
      //var_dump($idClienteMongo['data']['cliente']['_id']);

  }

      } catch(\Exception $e){


      }


    }

    function cargarEmpresas($idPais, $idCadena, $tipoEmpresa) {
        $this->lc_regs = [];

        switch ($tipoEmpresa) {
            case 'INTERNO':
                $lc_sql = "EXEC dbo.MOT_cargar_lista_empresas_internas $idPais ";
                break;
            case 'EXTERNO':
                $lc_sql = "EXEC dbo.MOT_cargar_lista_empresas_externas $idCadena";
                break;
            case 'AGREGADOR':
                $lc_sql = "EXEC dbo.MOT_cargar_lista_agregadores $idCadena";
                break;
        }

        try {
            $this->fn_ejecutarquery( $lc_sql );
            if ($this->fn_numregistro() > 0) {
                while ($row = $this->fn_leerarreglo()) {
                    $this->lc_regs[] = array(   "id"        => $row['id'],
                                                "empresa"   => $row['empresa']
                                            );
                }
                $this->lc_regs['registros'] = $this->fn_numregistro();
            }

        } catch (Exception $e) {
            return $e;
        }
        return $this->lc_regs;
    }

    function cargarTiposDocumentos(  ) {
        $this->lc_regs = [];
        $lc_sql = "EXEC [dbo].[TipoDocumentoMotorizado]";
        try {
            $this->fn_ejecutarquery( $lc_sql );
            if ($this->fn_numregistro() > 0) {
                while ( $row = $this->fn_leerarreglo() ) {
                    $this->lc_regs[] = array( "idTipoDocumento" => $row['idTipoDocumento'],
                                              "nombre"          => utf8_encode($row['nombre']),
                                              "codigo"          => $row['codigo']);
                }
                $this->lc_regs['registros'] = $this->fn_numregistro();
            }
        } catch (Exception $e) {
            return $e;
        }
        return $this->lc_regs;
    }
    function fn_buscarMotorizado($documento) {
        $this->lc_regs = array();
        $lc_sql = "EXEC [dbo].[BuscarMotorizado] '$documento'";
        try {
            $this->fn_ejecutarquery($lc_sql);
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs = array(
                    "IDMotorolo"        => $row['IDMotorolo'],
                    "estado"            => $row['estado'],
                    "tipo"              => strtoupper(utf8_encode(trim($row['tipo']))),
                    "empresa"           => strtoupper(utf8_encode(trim($row['empresa']))),
                    "IDTipoDocumento"   => $row['IDTipoDocumento'],
                    "documento"         => utf8_encode(trim($row['documento'])),
                    "IDCiudad"          => $row['IDCiudad'],
                    "nombres"           => utf8_encode(trim($row['nombres'])),
                    "apellidos"         => utf8_encode(trim($row['apellidos'])),
                    "codigoTarjeta"     => utf8_encode(trim($row['codigoTarjeta'])),
                    "codigoNomina"      => utf8_encode(trim($row['codigoNomina'])),
                    "telefono"          => utf8_encode(trim($row['telefono'])),
                    "replica"           => $row['documento']);
            }
            $this->lc_regs['str'] = $this->fn_numregistro();
        } catch (Exception $e) {
            return json_encode($e);
        }
        return $this->lc_regs;
    }

    function buscarMotorizado($url, $documento, $estado) {

        // Seteo de variables
        $respuesta = array(
            "type" => "",
            "codigo" => 0,
            "estado" => 0,
            "mensaje" => "",
            "motorolo" => array(
                "IDMotorolo"        => "",
                "estado"            => "",
                "tipo"              => "",
                "empresa"           => "",
                "IDTipoDocumento"   => "",
                "documento"         => "",
                "IDCiudad"          => "",
                "nombres"           => "",
                "apellidos"         => "",
                "codigoTarjeta"     => "",
                "codigoNomina"      => "",
                "telefono"          => "",
                "replica"           => ""
            )
        );

        try {
            $datosMotorizado = $this->fn_buscarMotorizado($documento);

            //Busqueda en Base de Datos Locals
            if ($datosMotorizado["str"] > 0) {
                $respuesta["estado"] = 3;
                $respuesta["mensaje"] = "baselocal";
                $respuesta["motorolo"]["IDMotorolo"]        = $datosMotorizado["IDMotorolo"];
                $respuesta["motorolo"]["estado"]            = $datosMotorizado["estado"];
                $respuesta["motorolo"]["tipo"]              = $datosMotorizado["tipo"];
                $respuesta["motorolo"]["empresa"]           = $datosMotorizado["empresa"];
                $respuesta["motorolo"]["IDTipoDocumento"]   = $datosMotorizado["IDTipoDocumento"];
                $respuesta["motorolo"]["documento"]         = $datosMotorizado["documento"];
                $respuesta["motorolo"]["IDCiudad"]          = $datosMotorizado["IDCiudad"];
                $respuesta["motorolo"]["nombres"]           = $datosMotorizado["nombres"];
                $respuesta["motorolo"]["apellidos"]         = $datosMotorizado["apellidos"];
                $respuesta["motorolo"]["codigoTarjeta"]     = $datosMotorizado["codigoTarjeta"];
                $respuesta["motorolo"]["codigoNomina"]      = $datosMotorizado["codigoNomina"];
                $respuesta["motorolo"]["telefono"]          = $datosMotorizado["telefono"];
                $respuesta["motorolo"]["replica"]           = $datosMotorizado["replica"];

            }
            //Busqueda en Servicio Centralizado
            else {

              $idCadena = $_SESSION['cadenaId'];

                $token = $this->validarTokenApiCliente();
                $apitoken = $token['token'];

                  $sql_url_base = "SELECT [config].[fn_ColeccionCadena_VariableV] ('".$idCadena."', 'MOTOROLO APIMDMCLIENTE', 'URL API MDM') AS url;";
                  $this->fn_ejecutarquery($sql_url_base);
                  $arreglo_url_base = $this->fn_leerarreglo();
                  $urlAPIMDM = $arreglo_url_base['url'];
                  // Iniciar cURL
                  $curl = curl_init();
                  //Opciones cURL
                  $options = array(
                      CURLOPT_URL => $urlAPIMDM . '/api/motorolo/' . $idCadena . '/' . $documento,
                      CURLOPT_RETURNTRANSFER => true,
                      CURLOPT_HTTPHEADER => array(
                          'Authorization: Bearer ' . $apitoken
                      ),
                  );
                  curl_setopt_array($curl, $options);
                  $response = curl_exec($curl);
                  $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                  curl_close($curl);
                  $datosApiMdmCliente = json_decode($response);
                  $respuestaSolicitud = $datosApiMdmCliente;


                if($http_status!=200) {
                    $respuesta["estado"]                        = 1;
                    $respuesta["mensaje"]                       = "baseremota";
                    $respuesta["motorolo"]["documento"]         = $documento;
                    $respuesta["motorolo"]["replica"]           = false;

                    return $respuesta;
                }

                //Existe Registro
                    if(!($respuestaSolicitud === null) && isset($respuestaSolicitud->status)){
                    if (($respuestaSolicitud->success == true) && (count(json_decode(json_encode($respuestaSolicitud->data),true))!=0)) {
                        $datosMotorizado = $respuestaSolicitud->data;
                        //var_dump();
                        $respuesta['estado']                        = 2;
                        $respuesta['mensaje']                       = 'baseremota';

                        if (!empty($datosMotorizado->dataMotorolo)) {
                            $motorolo = $datosMotorizado->dataMotorolo;
                            $respuesta['motorolo'] = array(
                                'estado' => isset($motorolo->status) ? $motorolo->status : null,
                                'tipo' => isset($motorolo->tipo_motorolo) ? $motorolo->tipo_motorolo : null,
                                'empresa' => isset($motorolo->empresa_motorolo) ? $motorolo->empresa_motorolo : null,
                                'codigoTarjeta' => isset($motorolo->codigo_tarjeta) ? $motorolo->codigo_tarjeta : null,
                                'codigoNomina' => isset($motorolo->codigo_nomina) ? $motorolo->codigo_nomina : null,
                                'telefono' => isset($motorolo->telefono) ? $motorolo->telefono : null
                            );
                        }

                        if (!empty($datosMotorizado->cliente)) {
                            $cliente = $datosMotorizado->cliente;
                            $respuesta['motorolo']['tipo']              ='INTERNO';
                            $respuesta['motorolo']['empresa']           = 'INT FOOD SERVICES CORP SA';
                            $respuesta['motorolo']['IDTipoDocumento'] = isset($cliente->tipoDocumento) ? $cliente->tipoDocumento : null;
                            $respuesta['motorolo']['documento'] = isset($cliente->documento) ? $cliente->documento : null;
                            $respuesta['motorolo']['nombres'] = isset($cliente->primerNombre) ? $cliente->primerNombre : null;
                            $respuesta['motorolo']['apellidos'] = isset($cliente->apellidos) ? $cliente->apellidos : null;

                        }

                        //$respuesta["motorolo"]["estado"]            = $datosMotorizado->dataMotorolo->status;
                        //$respuesta["motorolo"]["tipo"]              = $datosMotorizado->dataMotorolo->tipo_motorolo;
                        //$respuesta["motorolo"]["empresa"]           = $datosMotorizado->dataMotorolo->empresa_motorolo;
                        //$respuesta["motorolo"]["IDTipoDocumento"]   = $datosMotorizado->cliente->tipoDocumento;
                        //$respuesta["motorolo"]["documento"]         = $datosMotorizado->cliente->documento;
                        //$respuesta["motorolo"]["nombres"]           = $datosMotorizado->cliente->primerNombre;
                        //$respuesta["motorolo"]["apellidos"]         = $datosMotorizado->cliente->apellidos;
                        //$respuesta["motorolo"]["codigoTarjeta"]     = $datosMotorizado->dataMotorolo->codigo_tarjeta;
                        //$respuesta["motorolo"]["codigoNomina"]      = $datosMotorizado->dataMotorolo->codigo_nomina;
                        //$respuesta["motorolo"]["telefono"]          = $datosMotorizado->dataMotorolo->telefono;

                    } else if (($respuestaSolicitud->success == true)  && (count($respuestaSolicitud->data)==0)) {
                        $respuesta['estado']                        = 1;
                        $respuesta['mensaje']                       = 'baseremota';
                        $respuesta['motorolo']['documento']         = $documento;
                        $respuesta['motorolo']['replica']           = false;
                    }
                }
            }

            return $respuesta;
        } catch (Exception $e) {
            print json_encode($e);
        }
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


    function cargarUbicacionRestaurante( $idRestaurante ) {
        $this->lc_regs = array();
        $lc_sql = "EXEC [dbo].[UbicacionRestaurante] $idRestaurante;";

        try {
            $this->fn_ejecutarquery($lc_sql);
            if ( $this->fn_numregistro() > 0 ) {
                $row = $this->fn_leerarreglo();
                $this->lc_regs = array( "id_pais" => trim($row['id_pais']),
                "nombre_pais" => trim(utf8_encode($row['nombre_pais'])),
                "id_ciudad" => trim($row['id_ciudad']),
                "nombre_ciudad" => trim(utf8_encode($row['nombre_ciudad'])),
                "id_provincia" => trim($row['id_provincia']),
                "nombre_provincia" => trim(utf8_encode($row['nombre_provincia']))
             );
            }
        } catch (Exception $e) {
            return json_encode($e);
        }
        return $this->lc_regs;
    }

    function impresionDesasignacionMotorizado($id_motorizado, $id_periodo) {
        $lc_sql = "EXEC facturacion.App_impresion_dinamica_desasignacion_motorizado '$id_motorizado',  '$id_periodo'";
        return utf8_encode($this->fn_ejecutarquery($lc_sql));
    }

    function generarTokenApiMDMCliente() {
        $idRestaurante     = $_SESSION['rstId'];
        $idCadena          = $_SESSION['cadenaId'];

        $sql_url_base = "SELECT [config].[fn_ColeccionCadena_VariableV] ('".$idCadena."', 'MOTOROLO APIMDMCLIENTE', 'URL API MDM') AS url;";
        $this->fn_ejecutarquery($sql_url_base);
        $arreglo_url_base = $this->fn_leerarreglo();
        $urlAPIMDM = $arreglo_url_base['url'];

        $sql_idCliente = "SELECT [config].[fn_ColeccionCadena_VariableV] ('".$idCadena."', 'MOTOROLO APIMDMCLIENTE', 'APIMDMCLIENTID') AS idApi;";
        $this->fn_ejecutarquery($sql_idCliente);
        $arreglo_idCliente = $this->fn_leerarreglo();
        $api_clientID = $arreglo_idCliente['idApi'];

        $sql_secretCliente = "SELECT [config].[fn_ColeccionCadena_VariableV] ('".$idCadena."', 'MOTOROLO APIMDMCLIENTE', 'APIMDMCLIENTSECRET') AS secretApi;";
        $this->fn_ejecutarquery($sql_secretCliente);
        $arreglo_secretCliente  = $this->fn_leerarreglo();
        $api_secretCliente = $arreglo_secretCliente['secretApi'];

        $header_array = array(
            'Content-Type: application/json; charset=UTF-8',
            'Accept: application/json'
        );

        $credentials = array(
            'clientID' => $api_clientID,
            'clientSecret' => $api_secretCliente
        );
        $url = $urlAPIMDM . '/api/Auth/token';

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header_array);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($credentials));

        $result = curl_exec($ch);

        if ($result === false) {
            $result = 'cURL Error: ' . curl_error($ch);
        }

        curl_close($ch);

        return json_decode($result, true);

    }


    function validarTokenApiCliente() {
        $arrayToken = array();
        $mensaje = '';
        $token = '';
        $path_json = '';

        $fileName = 'tokenApiMdmCliente.json';
        $folderName = 'tokens';
        $permisos = '0777';
        $base_dir = realpath(__DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        $filePath = $base_dir . $folderName . DIRECTORY_SEPARATOR . $fileName;
        $filePathToken=$base_dir . $folderName;
        //Si no existe la carpeta tokens la crea
        if (!file_exists($filePathToken)) {
            mkdir($filePathToken, $permisos, true);
        }
        if (!file_exists($filePath)) {
          $tokenApiMDMCliente = $this->generarTokenApiMDMCliente();
          file_put_contents($filePath,$tokenApiMDMCliente);
        }


        $configContents =  file_get_contents($filePath);


        if ($configContents !== false) {
            $config = json_decode($configContents, true);

            if (isset($config['token'])) {
                $token = $config['token'];
                $tokenParts = explode('.', $token);
                $tokenPayload = base64_decode($tokenParts[1]);
                $payload = json_decode($tokenPayload, true);

                $tokenExpirationTime = $payload['exp'];
                $currentTimestamp = time();

                if ($currentTimestamp <= $tokenExpirationTime) {
                    $mensaje = utf8_decode("El token API MDM CLIENTE es válido y no ha caducado.");
                } else {
                    $tokenData = $this->generarTokenApiMDMCliente();

                    if (isset($tokenData["token"])) {
                        $token = $tokenData["token"];

                        $jsonDatos = json_encode($tokenData);
                       file_put_contents($filePath, $jsonDatos);
                    } else {
                        $mensaje = 'No se pudo generar un nuevo token.';
                    }
                }
            } else {
                $tokenData = $this->generarTokenApiMDMCliente();

                if (isset($tokenData["token"])) {
                    $token = $tokenData["token"];

                    $jsonDatos = json_encode($tokenData);
                    file_put_contents($filePath, $jsonDatos);

                } else {
                    $mensaje = 'No se pudo generar un nuevo token.';
                }
            }
        } else {
            $mensaje = "No se pudo leer el archivo JSON.";
        }

        $arrayToken = array('token' => $token, 'mensaje' => utf8_encode($mensaje));
        return $arrayToken;

    }

    public function obtenerTipoDocumento()
      {
      //  $sql_url_base = "SELECT tpdoc_descripcion FROM Motorolo WHERE IDTipoDocumento='030b9503-85cf-e511-80c6-000d3a3261f3' AS url;";
      //  $this->fn_ejecutarquery($sql_url_base);
      //  $arreglo_TipoDocumento = $this->fn_leerarreglo();
      //  $TipoDocumento = $arreglo_TipoDocumento['url'];
        //  return $TipoDocumento;
      }

    public function consultarClienteApi($documento){
            $idCadena = $_SESSION['cadenaId'];

              $token = $this->validarTokenApiCliente();
               try {
                $sql_url_base = "SELECT [config].[fn_ColeccionCadena_VariableV] ('".$idCadena."', 'MOTOROLO APIMDMCLIENTE', 'URL API MDM') AS url;";
                $this->fn_ejecutarquery($sql_url_base);
                $arreglo_url_base = $this->fn_leerarreglo();
                $urlAPIMDM = $arreglo_url_base['url'];

                // Iniciar cURL
                $curl = curl_init();
                //Opciones cURL
                $options = array(
                    CURLOPT_URL => $urlAPIMDM . '/api/motorolo/' . $idCadena . '/' . $documento,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_HTTPHEADER => array(
                        'Authorization: Bearer ' . $token['token']
                    ),
                );
                curl_setopt_array($curl, $options);
                $response = curl_exec($curl);
                curl_close($curl);
                $datosApiMdmCliente = json_decode($response, true);
               return $datosApiMdmCliente;


            } catch(\Exception $e){


            }
        }





}

?>
