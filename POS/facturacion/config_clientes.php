<?php

session_start();
//////////////////////////////////////////////////////////////
////////DESARROLLADO POR: Worman Andrade//////////////////////
////////DESCRIPCION: Archivo de Configuración para////////////
/////////////////////Clientes/////////////////////////////////
///////TABLAS INVOLUCRADAS://///////////////////////////////// 
///////FECHA CREACION: 19-02-2014/////////////////////////////
//////////////////////////////////////////////////////////////
///////FECHA ULTIMA MODIFICACION:21/04/2014///////////////////
///////USUARIO QUE MODIFICO: Jose Fernandez//////////////////
///////DECRIPCION ULTIMO CAMBIO: cargar ciudad segun la tienda/
//////////////////////////////////////////////////////////////

include("../system/conexion/clase_sql.php");
include("../clases/clase_clientes.php");

$lc_cliente = new cliente();

$restaurante = $_SESSION['rstId'];
$usuario = $_SESSION['usuarioId'];
$cadena = $_SESSION['cadenaId'];

if (htmlspecialchars(isset($_GET["clienteRepetido"]))) {
    $lc_datos[0] = htmlspecialchars($_GET["CedulaRepetido"]);
    print $lc_cliente->fn_clienteRepetido($lc_datos);
} else if (htmlspecialchars(isset($_POST["nuevoCliente"]))) {
    $lc_datos[0] = htmlspecialchars($_POST["accion"]);
    $lc_datos[1] = htmlspecialchars($_POST["clienteTipoDoc"]);
    $lc_datos[2] = htmlspecialchars($_POST["clienteDocumento"]);
    $lc_datos[3] = strtoupper(($_POST["clienteDescripcion"]));
    $lc_datos[4] = '';//strtoupper(utf8_decode(($_POST["clienteDireccion"])));
    $lc_datos[5] = htmlspecialchars($_POST["clienteFono"]);
    $lc_datos[6] = isset($_POST["clienteCorreo"]) ? strtolower($_POST["clienteCorreo"]) : "";
    $lc_datos[7] = htmlspecialchars($_POST["usuario"]);
    $lc_datos[8] = htmlspecialchars($_POST["estadoWS"]);
    $lc_datos[9] = htmlspecialchars($_POST["tipoCliente"]);
    print $lc_cliente->fn_registroClienteLocal($lc_datos);
} else if (htmlspecialchars(isset($_POST["guardarCliente"]))) {

    $lc_datos[0] = htmlspecialchars($_POST["accion"]);
    $lc_datos[1] = htmlspecialchars($_POST["tipoConsulta"]);
    $lc_datos[2] = htmlspecialchars($_POST["clienteTipoDoc"]);
    $lc_datos[3] = strtoupper(($_POST["clienteDocumento"]));
    $lc_datos[4] = strtoupper(($_POST["clienteDescripcion"]));
    $lc_datos[5] = '';
    $lc_datos[6] = strtolower($_POST["clienteFono"]);
    $lc_datos[7] = htmlspecialchars($_POST["clienteCorreo"]);
    $lc_datos[8] = htmlspecialchars($_POST["usuario"]);
    $lc_datos[9] = htmlspecialchars($_POST["estadoWS"]);
    $lc_datos[10] = htmlspecialchars($_POST["tipoCliente"]);
   
    $arr_json = $_POST["jsonDatosAdicionales"];
    $arr = array();
    foreach($arr_json as $clave => $valor){
        $arr[$clave] = utf8_encode(utf8_decode($valor));
    }
    $json = json_encode($arr,JSON_UNESCAPED_UNICODE);
 
    if( isset( $_POST["clienteTipoDoc"] ) && $_POST["clienteTipoDoc"] != 'CONSUMIDOR FINAL' )
    {

        if( isset( $_POST['accion'] ) && ( $_POST['accion'] == 'I' || $_POST['accion'] == 'U' ) )
        {

            if( isset( $_SESSION['rstId'] ) && $_SESSION['rstId'] != '' && $_SESSION['rstId'] != null )
            {

                $lcCliente = new cliente();

                $auxBinPHP = $lcCliente->rutaBinarioPHP( $_SESSION['rstId'] );

                if( $auxBinPHP["str"] === 1 && $auxBinPHP["rutaBinarioPHP"] != '' && $auxBinPHP["rutaBinarioPHP"] != null )
                {

                    // ejecutarSistemaDeDatosCentralizados
                    $ventana                        = 'start ';
                    $titulo                         = '"SistemaDeDatosCentralizados" ';
                    $parametrosStart                = '/b ';
                    $binPHP                         = trim( $auxBinPHP["rutaBinarioPHP"] ).' ';
                    $parametrosPHP                  = '-f ';
                    $SistemaDeDatosCentralizados    = '"'.str_replace( 'facturacion\config_clientes.php', '', str_replace( '/', '\\', $_SERVER['SCRIPT_FILENAME'] ) ) . 'facturacion\sistemaDeDatosClienteCentralizados.php'.'" ';
                    $argumentosSDC                  = '-- ';
                    $argvdocumentoIdentidad         = '"'.$_POST["clienteDocumento"].'" ';                                                                  
                    $argvtipoDocumentoIdentidad     = '"'.$_POST["clienteTipoDoc"].'" '; 
                    $argvnombres                    = '"'.$_POST["clienteDescripcion"].'" ';                                                                          
                    $argvcorreoElectronico          = '"'.$_POST["clienteCorreo"].'" ';                                                                           
                    $argvnumeroTelefono             = '"'.$_POST["clienteFono"].'" ';                                                                          
                    $argvautorizacion               = '"'.'PL'.'" ';
                    $argvsession_name               = '"'.session_name().'" ';
                    $argvsession_id                 = '"'.session_id().'" ';
                    $argvOperacion                  = '"'.'L.A.'.'" ';
                    $salidaSDC                      = '1> nul 2> nul';
                                                    
                    $sentencia = $ventana.$titulo.$parametrosStart.$binPHP.$parametrosPHP.$SistemaDeDatosCentralizados.$argumentosSDC.$argvdocumentoIdentidad.$argvtipoDocumentoIdentidad.$argvnombres.$argvcorreoElectronico.$argvnumeroTelefono.$argvautorizacion.$argvsession_name.$argvsession_id.$argvOperacion.$salidaSDC;
                    //Ejecuta la sentencia en un hilo hijo. El hilo padre no tendrá que esperar por el hilo hijo para concluir su proceso.
                    if( strtoupper( substr( PHP_OS, 0, 3 ) ) === 'WIN' )
                    {

                        pclose( popen( $sentencia, 'r' ) );

                    }/*
                    else 
                    {
                        //  UNIX
                        //          Definir binario PHP                         
                        $sentencia = $binPHP.$parametrosPHP.$SistemaDeDatosCentralizados.$argumentosSDC.$argvdocumentoIdentidad.$argvtipoDocumentoIdentidad.$argvnombres.$argvcorreoElectronico.$argvnumeroTelefono.$argvautorizacion.$argvsession_name.$argvsession_id.$argvOperacion;

                        exec( $sentencia . " > /dev/null &");
                    
                    }*/
                    //

                }

            }

        }

    }

    print $lc_cliente->fn_registroClienteLocalPayphone($lc_datos, $json);
    
    
} else if (htmlspecialchars(isset($_GET["consultabasefactura"]))) {
    $lc_datos = $cadena;
    $respuesta = $lc_cliente->fn_consultabasefactura($lc_datos);
    $_SESSION['fb_name'] = $respuesta["Cliente"];
    $_SESSION['fb_document'] = $respuesta['Documento'];
    print json_encode($respuesta);
} else if (htmlspecialchars(isset($_POST["actualizarDatosCliente"]))) {
    $lc_datos[0] = htmlspecialchars($_POST["accion"]);
    $lc_datos[1] = htmlspecialchars($_POST["clienteTipoDoc"]);
    $lc_datos[2] = htmlspecialchars($_POST["clienteDocumento"]);
    $lc_datos[3] = strtoupper(utf8_decode(($_POST["clienteDescripcion"])));
    $lc_datos[4] = '';//strtoupper(utf8_decode(($_POST["clienteDireccion"])));
    $lc_datos[5] = htmlspecialchars($_POST["clienteFono"]);
    $lc_datos[6] = (strtolower(utf8_decode($_POST["clienteCorreo"])));
    $lc_datos[7] = htmlspecialchars($_POST["usuario"]);
    $lc_datos[8] = htmlspecialchars($_POST["estadoWS"]);
    $lc_datos[9] = htmlspecialchars($_POST["tipoCliente"]);

        if( isset( $_POST["argvTDI"] ) && $_POST["argvTDI"] != 'CONSUMIDOR FINAL' )
        {

            if( isset( $_SESSION['rstId'] ) && $_SESSION['rstId'] != '' && $_SESSION['rstId'] != null )
            {

                $lcCliente = new cliente();

                $auxBinPHP = $lcCliente->rutaBinarioPHP( $_SESSION['rstId'] );

                if( $auxBinPHP["str"] === 1 && $auxBinPHP["rutaBinarioPHP"] != '' && $auxBinPHP["rutaBinarioPHP"] != null )
                {

                    // ejecutarSistemaDeDatosCentralizados
                    $ventana                        = 'start ';
                    $titulo                         = '"SistemaDeDatosCentralizados" ';
                    $parametrosStart                = '/b ';
                    $binPHP                         = trim( $auxBinPHP["rutaBinarioPHP"] ).' ';
                    $parametrosPHP                  = '-f ';
                    $SistemaDeDatosCentralizados    = '"'.str_replace( 'facturacion\config_clientes.php', '', str_replace( '/', '\\', $_SERVER['SCRIPT_FILENAME'] ) ) . 'facturacion\sistemaDeDatosClienteCentralizados.php'.'" ';
                    $argumentosSDC                  = '-- ';
                    $argvdocumentoIdentidad         = '"'.$_POST["clienteDocumento"].'" ';                                                                  
                    $argvtipoDocumentoIdentidad     = '"'.$_POST["argvTDI"].'" '; 
                    $argvnombres                    = '"'.$_POST["clienteDescripcion"].'" ';                                                                          
                    $argvcorreoElectronico          = '"'.$_POST["clienteCorreo"].'" ';                                                                           
                    $argvnumeroTelefono             = '"'.$_POST["clienteFono"].'" ';                                                                          
                    $argvautorizacion               = '"'.'PL'.'" ';
                    $argvsession_name               = '"'.session_name().'" ';
                    $argvsession_id                 = '"'.session_id().'" ';
                    $argvOperacion                  = '"'.'L.A.'.'" ';
                    $salidaSDC                      = '1> nul 2> nul';
                                                    
                    $sentencia = $ventana.$titulo.$parametrosStart.$binPHP.$parametrosPHP.$SistemaDeDatosCentralizados.$argumentosSDC.$argvdocumentoIdentidad.$argvtipoDocumentoIdentidad.$argvnombres.$argvcorreoElectronico.$argvnumeroTelefono.$argvautorizacion.$argvsession_name.$argvsession_id.$argvOperacion.$salidaSDC;
                    //Ejecuta la sentencia en un hilo hijo. El hilo padre no tendrá que esperar por el hilo hijo para concluir su proceso.
                    if( strtoupper( substr( PHP_OS, 0, 3 ) ) === 'WIN' )
                    {

                        pclose( popen( $sentencia, 'r' ) );

                    }/*
                    else 
                    {
                        //  UNIX
                        //          Definir binario PHP                         
                        $sentencia = $binPHP.$parametrosPHP.$SistemaDeDatosCentralizados.$argumentosSDC.$argvdocumentoIdentidad.$argvtipoDocumentoIdentidad.$argvnombres.$argvcorreoElectronico.$argvnumeroTelefono.$argvautorizacion.$argvsession_name.$argvsession_id.$argvOperacion;

                        exec( $sentencia . " > /dev/null &");
                    
                    }*/
                    //

                }

            }

        }

    print $lc_cliente->fn_registroClienteLocal($lc_datos);
} else if (htmlspecialchars(isset($_POST["solicitaCredencialesAdministrador"]))) {
    $lc_datos[0] = "C";
    $lc_datos[1] = $cadena;
    $lc_datos[2] = $restaurante;
    print $lc_cliente->fn_solicitaCredencialesAdministrador($lc_datos);
}

if (htmlspecialchars(isset($_POST["ConsumidorFinalDatos"]))) {
    $documento = htmlspecialchars($_POST["docConsumidor"]);
    $lc_cliente->fn_buscarCliente($documento);
    print json_encode($lc_cliente);
} else if (htmlspecialchars(isset($_POST["ConsumidorFinalDatos"]))) {
    $documento = htmlspecialchars($_POST["docConsumidor"]);
    $lc_cliente->fn_buscarCliente($documento);
    print json_encode($lc_cliente);
} else if (htmlspecialchars(isset($_POST["cargaDatosClienteKiosko"]))) {
    $cfac_id = htmlspecialchars($_POST["cfac_id"]);
    print $lc_cliente->fn_cargaDatosClienteKiosko($cfac_id);
} else if (htmlspecialchars(isset($_POST["guardaActualizaCliente"]))) {
    $clienteTipoDoc = htmlspecialchars($_POST["clienteTipoDoc"]);
    $clienteDocumento = htmlspecialchars($_POST["clienteDocumento"]);
    $clienteDescripcion = strtoupper(utf8_decode(($_POST["clienteDescripcion"])));
    $clienteDireccion = null;
    $clienteTelefono = htmlspecialchars($_POST["clienteTelefono"]);
    $clienteCorreo = (strtolower(utf8_decode($_POST["clienteCorreo"])));

        if( isset( $_POST["clienteTipoDoc"] ) && $_POST["clienteTipoDoc"] != 'CONSUMIDOR FINAL' )
        {
                        
            if( isset( $_SESSION['rstId'] ) && $_SESSION['rstId'] != '' && $_SESSION['rstId'] != null )
            {

                $lcCliente = new cliente();

                $auxBinPHP = $lcCliente->rutaBinarioPHP( $_SESSION['rstId'] );

                if( $auxBinPHP["str"] === 1 && $auxBinPHP["rutaBinarioPHP"] != '' && $auxBinPHP["rutaBinarioPHP"] != null )
                {

                    // ejecutarSistemaDeDatosCentralizados
                    $ventana                        = 'start ';
                    $titulo                         = '"SistemaDeDatosCentralizados" ';
                    $parametrosStart                = '/b ';
                    $binPHP                         = trim( $auxBinPHP["rutaBinarioPHP"] ).' ';
                    $parametrosPHP                  = '-f ';
                    $SistemaDeDatosCentralizados    = '"'.str_replace( 'facturacion\config_clientes.php', '', str_replace( '/', '\\', $_SERVER['SCRIPT_FILENAME'] ) ) . 'facturacion\sistemaDeDatosClienteCentralizados.php'.'" ';
                    $argumentosSDC                  = '-- ';
                    $argvdocumentoIdentidad         = '"'.$clienteDocumento.'" ';                                                                  
                    $argvtipoDocumentoIdentidad     = '"'.$clienteTipoDoc.'" ';                                                                           
                    $argvnombres                    = '"'.$clienteDescripcion.'" ';                                                                          
                    $argvcorreoElectronico          = '"'.$clienteCorreo.'" ';                                                                           
                    $argvnumeroTelefono             = '"'.$clienteTelefono.'" ';                                                                          
                    $argvautorizacion               = '"'.'PL'.'" ';
                    $argvsession_name               = '"'.session_name().'" ';
                    $argvsession_id                 = '"'.session_id().'" ';
                    $argvOperacion                  = '"'.'L.A.'.'" ';
                    $salidaSDC                      = '1> nul 2> nul';
                                                    
                    $sentencia = $ventana.$titulo.$parametrosStart.$binPHP.$parametrosPHP.$SistemaDeDatosCentralizados.$argumentosSDC.$argvdocumentoIdentidad.$argvtipoDocumentoIdentidad.$argvnombres.$argvcorreoElectronico.$argvnumeroTelefono.$argvautorizacion.$argvsession_name.$argvsession_id.$argvOperacion.$salidaSDC;
                    //Ejecuta la sentencia en un hilo hijo. El hilo padre no tendrá que esperar por el hilo hijo para concluir su proceso. 
                    if( strtoupper( substr( PHP_OS, 0, 3 ) ) === 'WIN' )
                    {

                        pclose( popen( $sentencia, 'r' ) );

                    }/*
                    else 
                    {
                        //  UNIX
                        //          Definir binario PHP                         
                        $sentencia = $binPHP.$parametrosPHP.$SistemaDeDatosCentralizados.$argumentosSDC.$argvdocumentoIdentidad.$argvtipoDocumentoIdentidad.$argvnombres.$argvcorreoElectronico.$argvnumeroTelefono.$argvautorizacion.$argvsession_name.$argvsession_id.$argvOperacion;

                        exec( $sentencia . " > /dev/null &");
                    
                    }*/
                    //

                }

            }

        }

    print $lc_cliente->fn_guardaActualizaCliente($clienteTipoDoc, $clienteDocumento, $clienteDescripcion, $clienteDireccion, $clienteTelefono, $clienteCorreo, $usuario);
}