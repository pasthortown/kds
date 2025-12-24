<?php

    $session_name   = trim( $argv[7] );
    $session_id     = trim( $argv[8] );

    $_COOKIE[$session_name] = $session_id;

    session_start();

    require_once( "../system/conexion/clase_sql.php" );
    require_once( "../clases/clase_clientes.php" );
    require_once( "../clases/clase_webservice.php" );
                                       
    $servicioWebObj     = new webservice(); 
    
    //Parámetros
    $documentoIdentidad     = trim( $argv[1] );                                                                        
    $tipoDocumentoIdentidad = trim( $argv[2] );                                                                         
    $nombres                = trim( $argv[3] );                                                                        
    $correoElectronico      = trim( $argv[4] );                                                                         
    $numeroTelefono         = trim( $argv[5] );                                                                        
    $autorizacion           = trim( $argv[6] );                                                                         
    $IDrestaurante          = trim( $_SESSION["rstId"] );  
    //
    $Operacion              = trim( $argv[9] );    

    //
    $descripcionServicioWeb_Cli_Cli = $servicioWebObj->retorna_WS_Clientes_Cliente( $IDrestaurante );
    $estadoServicioWeb              = $descripcionServicioWeb_Cli_Cli["estado"];

    if( $estadoServicioWeb != 0 )
    {
        
        if( $Operacion == 'L.A.' )
        {

            $resultadoProcesoL = procesoL( $descripcionServicioWeb_Cli_Cli, $documentoIdentidad, $tipoDocumentoIdentidad );

            if ( $resultadoProcesoL['estadoPL'] != 0 )
            {

                $descripcionServicioWeb_Cli_Act = $servicioWebObj->retorna_WS_Clientes_Actualiza( $IDrestaurante );  

                $autorizacion = $resultadoProcesoL['autorizacionPl'];

                procesoA( $documentoIdentidad, $tipoDocumentoIdentidad, $nombres, $correoElectronico, $numeroTelefono, $autorizacion, $descripcionServicioWeb_Cli_Act );

            }

        }
        else if( $Operacion == '' )
        {

            ;
            
        }

    }

    // ----------------------------------------------------------------------------------------------------------------------------------------------

    function procesoL( $descripcionServicioWeb_Cli_Cli, $documentoIdentidad, $tipoDocumentoIdentidad )
    {

        $estadoPL = 0;
        $autorizacionPl = '';
        //
        $URL_SW = trim( $descripcionServicioWeb_Cli_Cli["urlwebservice"] );
        
        $manipulador_Curl = curl_init( $URL_SW );

            curl_setopt( $manipulador_Curl, CURLOPT_CUSTOMREQUEST,  "POST" );
            curl_setopt( $manipulador_Curl, CURLOPT_POSTFIELDS,     json_encode( ['identificacion' => $documentoIdentidad, 'tipoIdentificacion' => $tipoDocumentoIdentidad] ) );
            curl_setopt( $manipulador_Curl, CURLOPT_RETURNTRANSFER, true );
            curl_setopt( $manipulador_Curl, CURLOPT_HTTPHEADER,     array( 'Content-Type: application/json', 'Accept: application/json' ) );
            curl_setopt( $manipulador_Curl, CURLOPT_TIMEOUT,        5 );
            curl_setopt( $manipulador_Curl, CURLOPT_CONNECTTIMEOUT, 5 );

        $ResultadoCurlExec = curl_exec( $manipulador_Curl );

        $HTTP_response_StatusCode = curl_getinfo( $manipulador_Curl, CURLINFO_HTTP_CODE );

        curl_close($manipulador_Curl);
        //
        if ( $HTTP_response_StatusCode == 200 ) 
        {

            $curlExecDecodeResul = json_decode( $ResultadoCurlExec );

            if ( $curlExecDecodeResul->estado != 0 )
            {
                
                $CurlExec_DatosCliente  = $curlExecDecodeResul->cliente['0'];

                $autorizacionPl = $CurlExec_DatosCliente->autorizacion;
                    
                if ( $autorizacionPl != '' && $autorizacionPl != null )
                {

                    if ( $curlExecDecodeResul->estado === 1 ) 
                    {

                        $estadoPL = $curlExecDecodeResul->estado;

                    } 
                    else if ( $curlExecDecodeResul->estado === 2 ) 
                    {

                        $estadoPL = $curlExecDecodeResul->estado;

                    }

                }

            }

        }
        
        return $rPL = array( 'estadoPL' => $estadoPL, 'autorizacionPl' => $autorizacionPl );

    }

    function procesoA( $documentoIdentidad, $tipoDocumentoIdentidad, $nombres, $correoElectronico, $numeroTelefono, $autorizacion, $descripcionServicioWeb_Cli_Act ) 
    {

        $descripcionCliente = array(
            "alias"                         => "",
            "nombre"                        => "",
            "apellido"                      => "",
            "celular"                       => "",
            "direccionDomicilio"            => "",                 
            "fechaNacimiento"               => "",
            "fechaUltimaActualizacion"      => "",
            "identificacion"                => $documentoIdentidad,
            "tipoIdentificacion"            => $tipoDocumentoIdentidad,
            "descripcion"                   => $nombres,
            "correo"                        => $correoElectronico,
            "telefonoDomiclio"              => $numeroTelefono,
            "autorizacion"                  => $autorizacion
        );

        $datos = json_encode( $descripcionCliente );
              
        $URLsw = trim( $descripcionServicioWeb_Cli_Act["urlwebservice"] );

        //
        $manipuladorCurl = curl_init( $URLsw );

            curl_setopt( $manipuladorCurl, CURLOPT_CUSTOMREQUEST,    "POST" );
            curl_setopt( $manipuladorCurl, CURLOPT_POSTFIELDS,       $datos );
            curl_setopt( $manipuladorCurl, CURLOPT_RETURNTRANSFER,   true );
            curl_setopt( $manipuladorCurl, CURLOPT_HTTPHEADER,       array( "Content-Type: application/json", "Accept: application/json" ) );
            curl_setopt( $manipuladorCurl, CURLOPT_TIMEOUT,          50 );
            curl_setopt( $manipuladorCurl, CURLOPT_CONNECTTIMEOUT,   50 );

        $curlExec = curl_exec( $manipuladorCurl );
        $curlExecDecode = json_decode( $curlExec );

        $HTTPresponseStatusCode = curl_getinfo( $manipuladorCurl, CURLINFO_HTTP_CODE );

        curl_close( $manipuladorCurl );
        //

        if ( $HTTPresponseStatusCode != 200 ) 
        {

            $clienteObj = new cliente();

            $clienteObj->fn_actualizarEstado_WS( 3, $documentoIdentidad, "RC202" ); 

        }

    }

?>