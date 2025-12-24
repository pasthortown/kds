<?php

session_start();
//include ("../system/seguridad.inc");
///////////////////////////////////////////////////////////////////////////////
///////DESARROLLADO POR:      JOSE FERNANDEZ //////////////////////////////////
///////DESCRIPCION:           Archivo de configuracion del Modulo FIN DE DIA //
///////TABLAS:                ARQUEO_CAJA,BILLETE_ESTACION, ///////////////////
//////////////////////////////CONTROL_ESTACION,ESTACION ///////////////////////
//////////////////////////////BILLETE_DENOMINACION ////////////////////////////
////////FECHA CREACION:       20/12/2013///////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////
///////MODIFICADO POR:        JIMMY CAZARO ////////////////////////////////////
///////DESCRIPCION:           Para recuperar las mesas y las cuentas //////////
////////TABLAS: ///////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////
///////MODIFICADO POR:        JUAN ESTEBAN CANELOS ////////////////////////////
///////FECHA MODIFICACION:    26/03/2018 //////////////////////////////////////
///////DESCRIPCION:           Validación restaurantes 24 horas ////////////////
///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////
///////MODIFICADO POR:        XAVIER AUCANSHALA P. ////////////////////////////
///////FECHA MODIFICACION:    15/10/2019 //////////////////////////////////////
///////DESCRIPCION:           Configuraciones y Desmontar Estacion Pickup /////
///////////////////////////////////////////////////////////////////////////////

include_once("../system/conexion/clase_sql.php");
include_once ("../clases/clase_corteCaja.php");
$lc_apertura = new corteCaja();




$idCadena = $_SESSION['cadenaId'];
$idPeriodo = $_SESSION['IDPeriodo'];
$lc_cadena=$_SESSION['cadenaId']; // FIN cambios PICKUP
$lc_rest = $_SESSION['rstId'];
$lc_usuarioId = $_SESSION['usuarioId'];
$ip = $_SESSION['direccionIp'];
$lc_usuarioIdAdmin = $_SESSION['usuarioIdAdmin']; //$lc_usuarioIdAdmin = isset($_SESSION['usuarioIdAdmin'])?$_SESSION['usuarioIdAdmin']:null;
$lc_est_id = isset($_SESSION['estacionId'])?$_SESSION['estacionId']:null;

if (htmlspecialchars(isset($_GET["consultaEstacion"]))) {
    $lc_condiciones[0] = 'C';
    $lc_condiciones[1] = $lc_rest;  // FIN cambios PICKUP
    $lc_condiciones[2] = 0;
    print $lc_apertura->fn_consultar("consultaEstacion", $lc_condiciones);
    
    /* Devuelve registros de control estación dada una fecha *//////////////////
} else if (htmlspecialchars(isset($_GET["devuelveControlEstacion"]))){
    print $lc_apertura->fn_consultar("devuelveControlEstacion",htmlspecialchars($_GET["fechaAperturaPeriodo"]));
    
    /* Devuelve registros de control estación dada una fecha *//////////////////
} else if (htmlspecialchars(isset($_GET["devuelveTotalVentaPorEstacion"]))){
 
    $lc_condiciones[0] = htmlspecialchars($_GET["fechaAperturaPeriodo"]);
    print $lc_apertura->fn_consultar("devuelveTotalVentaPorEstacion",$lc_condiciones);
    /*devuelve politica activa */
}  else if (htmlspecialchars(isset($_GET["politicaControlCajaActiva"]))){ 
    $lc_condiciones[0] = htmlspecialchars($_GET["rst_id"]);
    print $lc_apertura->fn_consultar("politicaControlCajaActiva",$lc_condiciones );    
} 

else if (htmlspecialchars(isset($_GET["consultaMotorizados"]))) {
    $lc_condiciones[0] = $idCadena;
    $lc_condiciones[1] = $lc_rest;
    $lc_condiciones[2] = $idPeriodo;
    print $lc_apertura->fn_consultar("consultaMotorizados", $lc_condiciones);
    
} 
else if (htmlspecialchars(isset($_GET["obtenerUrlSir"]))) {
 
    print $lc_apertura->obtenerUrlSir();
    
} 
else if (htmlspecialchars(isset($_GET["consultaPedidosApp"]))) {
    $lc_condiciones[0] = $idCadena;
    $lc_condiciones[1] = $lc_rest;
    $lc_condiciones[2] = $idPeriodo;
    $lc_condiciones[3] = 'PRINCIPAL';
    print $lc_apertura->fn_consultar("consultaPedidosApp", $lc_condiciones);
    
} else if (htmlspecialchars(isset($_GET["validaEstaciondesmontado"]))) {
    $lc_condiciones[0] = 'D';
    $lc_condiciones[1] = $lc_rest;
    $lc_condiciones[2] = htmlspecialchars($_GET["codigo"]);
    print $lc_apertura->fn_consultar("validaEstaciondesmontado", $lc_condiciones);

    
/* ----------------------------------------------------------------------------------------------------
  Consulta numero de mesa de una orden para recuperarla
  ----------------------------------------------------------------------------------------------------- */
} else if (htmlspecialchars(isset($_GET["cargaSesionesFinDia"]))) {
    $_SESSION['usuarioId'] = htmlspecialchars($_GET["cajeroSesion"]);
    $_SESSION['usuarioIdAdmin'] = htmlspecialchars($_GET["administradorSesion"]);
    return true;
    //header('Location: desmontado_cajero.php');

} else if (htmlspecialchars(isset($_GET["consultarMesaOrden"]))) {
    $lc_condiciones[0] = 4;
    $lc_condiciones[1] = '0';
    $lc_condiciones[2] = trim($_GET["odp_id"], '"');
    print $lc_apertura->fn_consultar("consultarMesaOrden", $lc_condiciones);

/* ------------------------------------------------------------------------------------------------------
  retorna el  id del id de la nota de credito filtrado en el id de la factura
  ------------------------------------------------------------------------------------------------------- */
} else if (htmlspecialchars(isset($_GET["retomarCuentaAbierta"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["accion"]);
    $lc_condiciones[1] = trim($_GET["cfac_id"], '"');
    $lc_condiciones[2] = '0';
    print $lc_apertura->fn_consultar("retomarCuentaAbierta", $lc_condiciones);

}else if (htmlspecialchars(isset($_GET["consultaPedidosApp1"]))) {
    $lc_condiciones[0] = $idCadena;
    $lc_condiciones[1] = $lc_rest;
    $lc_condiciones[2] = $idPeriodo;
    $lc_condiciones[3] = 'PENDIENTE';
    print $lc_apertura->fn_consultar("consultaPedidosApp", $lc_condiciones);
    
} else if (htmlspecialchars(isset($_GET["consultadetalleCuenta"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["accion"]);
    $lc_condiciones[1] = trim($_GET["idfacDetalle"], '"');
    $lc_condiciones[2] = '0';
    print $lc_apertura->fn_consultar("consultadetalleCuenta", $lc_condiciones);

} else if (htmlspecialchars(isset($_GET["inserta_canal_desmontado"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["ctrcDesmontado"]);
    $lc_condiciones[1] = $lc_usuarioId;
    $lc_condiciones[2] = $ip;
    print $lc_apertura->fn_ejecutar("inserta_canal_desmontado", $lc_condiciones);

} else if (htmlspecialchars(isset($_GET["resumenFormaPago"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["cfactura"]);
    print $lc_apertura->fn_consultar("resumenFormaPago", $lc_condiciones);

} else if (htmlspecialchars(isset($_GET["consultatotalesCuenta"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["accion"]);
    $lc_condiciones[1] = trim($_GET["idfacDetalle"], '"');
    $lc_condiciones[2] = '0';
    print $lc_apertura->fn_consultar("consultatotalesCuenta", $lc_condiciones);

} else if (htmlspecialchars(isset($_GET["consultaformaPago"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["codigo"]);
    $lc_condiciones[1] = htmlspecialchars($_GET["codigoUsuario"]);
    $lc_condiciones[2] = $lc_rest;
    print $lc_apertura->fn_consultar("consultaformaPago", $lc_condiciones);

} else if (htmlspecialchars(isset($_GET["consultaformaPagoModificado"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["id_Estacion"]);
    $lc_condiciones[1] = htmlspecialchars($_GET["id_usuariO"]);
    $lc_condiciones[2] = $lc_rest;
    $lc_condiciones[3] = htmlspecialchars($_GET["formaPago"]);
    print $lc_apertura->fn_consultar("consultaformaPagoModificado", $lc_condiciones);

} else if (htmlspecialchars(isset($_GET["calculatotalesMofificados"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["idestacion"]);
    $lc_condiciones[1] = /* $lc_usuarioId;// */$_GET["userId"];
    $lc_condiciones[2] = $lc_rest;
    print $lc_apertura->fn_consultar("calculatotalesMofificados", $lc_condiciones);
    
//CU8ENTAS ABIERTAS
} else if (htmlspecialchars(isset($_GET["consultaMesa"]))) {
    $lc_condiciones[0] = 'C';
    $lc_condiciones[1] = $lc_rest;
    $lc_condiciones[2] = 0;
    $lc_condiciones[3] = $lc_usuarioId;
$lc_condiciones[4] = 0;
    print $lc_apertura->fn_consultar("consultaMesa", $lc_condiciones);

//CUENTAS ABIERTAS FullServices
} else if (htmlspecialchars(isset($_GET["consultaMesaFS"]))) {
    $lc_condiciones[0] = 'FS';
    $lc_condiciones[1] = $lc_rest;
    $lc_condiciones[2] = 0;
    $lc_condiciones[3] = $lc_usuarioId;
    $lc_condiciones[4] = htmlspecialchars($_GET["cod_piso"]);
    $lc_condiciones[5] = htmlspecialchars($_GET["cod_area"]);
    print $lc_apertura->fn_consultar("consultaMesaFS", $lc_condiciones);

} else if (htmlspecialchars(isset($_GET["consultadetalleMesa"]))) {
    $lc_condiciones[0] = 'D';
    $lc_condiciones[1] = $lc_rest;
    $lc_condiciones[2] = htmlspecialchars($_GET["codigoOrden"]);
    $lc_condiciones[3] = $lc_usuarioId;
$lc_condiciones[4] = $_GET["cuenta"];
    print $lc_apertura->fn_consultar("consultadetalleMesa", $lc_condiciones);

} else if (htmlspecialchars(isset($_GET["consultatotalesMesa"]))) {
    $lc_condiciones[0] = 'T';
    $lc_condiciones[1] = $lc_rest;
    $lc_condiciones[2] = htmlspecialchars($_GET["codigoOrden"]);
    $lc_condiciones[3] = $lc_usuarioId;
$lc_condiciones[4] = $_GET["cuenta"];
    print $lc_apertura->fn_consultar("consultatotalesMesa", $lc_condiciones);

} else if (htmlspecialchars(isset($_GET["validacuenta"]))) {
    $lc_condiciones[0] = 'V';
    $lc_condiciones[1] = $lc_rest;
    $lc_condiciones[2] = 0;
    $lc_condiciones[3] = $lc_usuarioId;
$lc_condiciones[4] = 0;
    print $lc_apertura->fn_consultar("validacuenta", $lc_condiciones);

} else if (isset($_GET["limpiarcuentacero"])) {
    $lc_condiciones[0] = $lc_usuarioIdAdmin;
    $lc_condiciones[1] = $lc_rest;
    print $lc_apertura->fn_consultar("limpiarcuentacero", $lc_condiciones);

//CUENTAS POR FACTURAR
} else if (htmlspecialchars(isset($_GET["consultaCuenta"]))) {
    $lc_condiciones[0] = $lc_rest;
    print $lc_apertura->fn_consultar("consultaCuenta", $lc_condiciones);
    
} else if (htmlspecialchars(isset($_GET["consultatotalEstacion"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["codigo"]);
    $lc_condiciones[1] = $lc_rest;
    print $lc_apertura->fn_consultar("consultatotalEstacion", $lc_condiciones);

} else if (htmlspecialchars(isset($_GET["consultaidBilletes"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["top"]);
    print $lc_apertura->fn_consultar("consultaidBilletes", $lc_condiciones);

} else if (htmlspecialchars(isset($_GET["consultatotalformaPago"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["codigo"]);
    $lc_condiciones[1] = htmlspecialchars($_GET["idforma"]);
    $lc_condiciones[2] = $lc_rest;
    print $lc_apertura->fn_consultar("consultatotalformaPago", $lc_condiciones);

} else if (htmlspecialchars(isset($_GET["consultaformaPagoModificadoTarjeta"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["estaciond"]);
    $lc_condiciones[1] = htmlspecialchars($_GET["id_User"]);
    $lc_condiciones[2] = $lc_rest;
    $lc_condiciones[3] = htmlspecialchars($_GET["formaPago"]);
    print $lc_apertura->fn_consultar("consultaformaPagoModificadoTarjeta", $lc_condiciones);

} else if (htmlspecialchars(isset($_GET["consultaBilletes"]))) {
    print $lc_apertura->fn_consultar("consultaBilletes", '');

} else if (htmlspecialchars(isset($_GET["consultaBilletesModificados"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["id_User"]);
    $lc_condiciones[1] = $lc_rest;
    print $lc_apertura->fn_consultar("consultaBilletesModificados", $lc_condiciones);

} else if (htmlspecialchars(isset($_GET["totalesPos"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["t_estacion"]);
    $lc_condiciones[1] = htmlspecialchars($_GET["t_idUsuario"]);
    $lc_condiciones[2] = $lc_rest;
    print $lc_apertura->fn_consultar("totalesPos", $lc_condiciones);

} else if (htmlspecialchars(isset($_GET["totalesIngresados"]))) {
    $lc_condiciones[0] = /* $lc_usuarioId;// */$_GET["User"];
    $lc_condiciones[1] = $lc_rest;
    $lc_condiciones[2] = /* $lc_usuarioId;// */$_GET["formapago"];
    print $lc_apertura->fn_consultar("totalesIngresados", $lc_condiciones);
            // cambios PICKUP
} else if (htmlspecialchars(isset($_GET["PickupConfiguracionEstacion"]))) {
    $lc_condiciones[0] = $lc_cadena; //10
    $lc_condiciones[1] = $lc_rest;  //40
    print $lc_apertura->fn_consultar("PickupConfiguracionEstacion", $lc_condiciones);    
    
}else if (htmlspecialchars(isset($_GET["traeUsuarioAdmin"]))) {
        $lc_condiciones[0] = htmlspecialchars($_GET["accion"]);
        $lc_condiciones[1] = $lc_usuarioIdAdmin;
        $lc_condiciones[2] = $lc_est_id;
        print $lc_apertura->fn_consultar("traeUsuarioAdmin", $lc_condiciones);
// FIN cambios PICKUP
} else if (htmlspecialchars(isset($_GET["grabaBilletes"]))) {
    //$lc_condiciones6=$_GET["resNuevo"];
    $lc_condiciones5 = htmlspecialchars($_GET["cantidades2"]); //cantidad de billetes
    $lc_condiciones = htmlspecialchars($_GET["resultado2"]); //valor total de billetes
    $lc_condiciones3 = htmlspecialchars($_GET["oculto"]); //id de billete
    $lc_condiciones4 = htmlspecialchars($_GET["oculto2"]); //id de control estacion
    $lc_condiciones6 = htmlspecialchars($_GET["oculto3"]); //id de usuario		
    for ($i = 0; $i < count($lc_condiciones5); $i++) {
        $lc_condiciones2[0] = $lc_condiciones[$i];
        $lc_condiciones2[1] = $lc_condiciones3[$i]; //id de billete
        $lc_condiciones2[2] = $lc_condiciones4[$i];
        $lc_condiciones2[3] = $lc_condiciones5[$i];
        $lc_condiciones2[4] = $lc_condiciones6[$i];
        print $lc_apertura->fn_ejecutar("grabaBilletes", $lc_condiciones2);
    }

/*} else if(isset($_GET["auditoriaEfectivo"]))
  {
  $lc_condiciones10[0]=$_GET["auditoriaTotal"];
  $lc_condiciones10[4]=$lc_rest;
  $lc_condiciones10[5]=$lc_usuarioId;
  print $lc_apertura->fn_ejecutar("auditoriaEfectivo",$lc_condiciones10);
  */
  //  cambios PICKUP
} else if (htmlspecialchars(isset($_GET["desmontarEstacionPickup"]))) {
    $lc_condiciones[0] = $lc_cadena; //10
    $lc_condiciones[1] = $lc_rest;  //40
    $lc_condiciones[2] = $_SESSION['usuarioId'];
    print $lc_apertura->fn_ejecutar("desmontarEstacionPickup", $lc_condiciones);
}
else if (htmlspecialchars(isset($_GET["imprimeDesmontadoCajeroPickUp"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["usr_id"]);
    $lc_condiciones[1] = htmlspecialchars($_GET["ctrc_id"]);
    $lc_condiciones[2] = $lc_usuarioIdAdmin;
    // print_r($lc_condiciones);
    print $lc_apertura->fn_ejecutar("imprimeDesmontadoCajeroPickUp", $lc_condiciones);
// cambios PICKUP
} else if (htmlspecialchars(isset($_GET["auditoriaEfectivo"]))) {
    $lc_condiciones10[0] = 'AA';
    $lc_condiciones10[4] = htmlspecialchars($_GET["auditoriaTotal"]);
    $lc_condiciones10[5] = $lc_rest;
    $lc_condiciones10[6] = $lc_usuarioId;
    $lc_condiciones10[7] = 'Efectivo';
    $lc_condiciones10[8] = 'Cajero';
    $lc_condiciones10[9] = 'Motivo';
    print $lc_apertura->fn_ejecutar("auditoriaEfectivo", $lc_condiciones10);

} else if (htmlspecialchars(isset($_GET["auditoriaEfectivoModificado"]))) {
    $lc_condiciones10[0] = htmlspecialchars($_GET["auditoriaTotal"]);
    $lc_condiciones10[4] = $lc_rest;
    $lc_condiciones10[5] = $lc_usuarioId;
    print $lc_apertura->fn_ejecutar("auditoriaEfectivoModificado", $lc_condiciones10);

} else if (htmlspecialchars(isset($_GET["auditoriaTarjeta"]))) {
    $lc_condiciones[0] = 'AT';
    $lc_condiciones[1] = $lc_usuarioId;
    $lc_condiciones[2] = $lc_rest;
    $lc_condiciones[3] = htmlspecialchars($_GET["totalTarjeta"]);
    $lc_condiciones[4] = strtoupper(utf8_decode($_GET["tipoTarjeta"]));
    $lc_condiciones[5] = 'Cajero';
    $lc_condiciones[6] = 'Motivo';
    print $lc_apertura->fn_ejecutar("auditoriaTarjeta", $lc_condiciones);

} else if (htmlspecialchars(isset($_GET["auditoriaCancelar"]))) {
    $lc_condiciones[0] = 'AC';
    $lc_condiciones[1] = $lc_usuarioId;
    $lc_condiciones[2] = $lc_rest;
    $lc_condiciones[3] = htmlspecialchars($_GET["total"]);
    $lc_condiciones[4] = 'Todos';
    $lc_condiciones[5] = 'Cajero';
    $lc_condiciones[6] = 'Motivo';
    print $lc_apertura->fn_ejecutar("auditoriaCancelar", $lc_condiciones);

} else if (htmlspecialchars(isset($_GET["auditoriaCajero"]))) {
    $lc_condiciones[0] = 'CA';
    $lc_condiciones[1] = $lc_usuarioId;
    $lc_condiciones[2] = $lc_rest;
    $lc_condiciones[3] = 0;
    $lc_condiciones[4] = 'Todos';
    $lc_condiciones[5] = htmlspecialchars($_GET["usuarioCajero"]);
    $lc_condiciones[6] = 'Motivo';
    print $lc_apertura->fn_ejecutar("auditoriaCajero", $lc_condiciones);

} else if (htmlspecialchars(isset($_GET["auditoriaCajeroMotivo"]))) {
    $lc_condiciones[0] = 'AM';
    $lc_condiciones[1] = $lc_usuarioId;
    $lc_condiciones[2] = $lc_rest;
    $lc_condiciones[3] = 0;
    $lc_condiciones[4] = 'Todos';
    $lc_condiciones[5] = htmlspecialchars($_GET["usuarioCajero"]);
    $lc_condiciones[6] = strtoupper(utf8_decode($_GET["motivo"]));
    print $lc_apertura->fn_ejecutar("auditoriaCajeroMotivo", $lc_condiciones);

} else if (htmlspecialchars(isset($_GET["grabaArqueo"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["accion"]);
    $lc_condiciones[1] = htmlspecialchars($_GET["idUsuario"]);
    $lc_condiciones[2] = htmlspecialchars($_GET["formaPago"]);
    $lc_condiciones[3] = htmlspecialchars($_GET["arqueovalor"]);
    $lc_condiciones[4] = '';
    $lc_condiciones[5] = 0;
    $lc_condiciones[6] = $lc_rest;
    print $lc_apertura->fn_ejecutar("grabaArqueo", $lc_condiciones);

} else if (htmlspecialchars(isset($_GET["grabaarqueotarjeta"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["accion"]);
    $lc_condiciones[1] = htmlspecialchars($_GET["idUser"]);
    $lc_condiciones[2] = htmlspecialchars($_GET["idPago"]);
    $lc_condiciones[3] = htmlspecialchars($_GET["totaltarjeta"]);
    $lc_condiciones[4] = '';
    $lc_condiciones[5] = 0;
    $lc_condiciones[6] = $lc_rest;
    print $lc_apertura->fn_ejecutar("grabaarqueotarjeta", $lc_condiciones);

/* modificado el 13/05/2014 *//////////////////
} else if (htmlspecialchars(isset($_GET["grabaCanalCierraSistema"]))) {
    $lc_condiciones[0] = 'B';
    $lc_condiciones[1] = $ip;
    $lc_condiciones[2] = $lc_usuarioId; //				
    print $lc_apertura->fn_ejecutar("grabaCanalCierraSistema", $lc_condiciones);

} else if (htmlspecialchars(isset($_GET["eliminaBilletes"]))) {
    $lc_condiciones = htmlspecialchars($_GET["eliminaBillete"]);
    for ($i = 0; $i < count($lc_condiciones); $i++) {
        $lc_condiciones2[0] = $lc_condiciones[$i];
        print $lc_apertura->fn_ejecutar("eliminaBilletes", $lc_condiciones2);
    }

} else if (htmlspecialchars(isset($_GET["consultaTarjeta"]))) {
    $lc_condiciones[0] = $lc_rest;
    $lc_condiciones[1] = htmlspecialchars($_GET["idEstacion"]);
    $lc_condiciones[2] = htmlspecialchars($_GET["idPago"]);
    print $lc_apertura->fn_consultar("consultaTarjeta", $lc_condiciones);

} else if (htmlspecialchars(isset($_GET["eliminaArqueo"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["estacionElimina"]);
    $lc_condiciones[1] = $lc_usuarioId; //$_GET["usuarioID"];	
    $lc_condiciones[2] = $lc_rest;
    print $lc_apertura->fn_ejecutar("eliminaArqueo", $lc_condiciones);

} else if (htmlspecialchars(isset($_GET["actualizaCajero"]))) {
    $lc_condiciones[0] = 'UC';
    $lc_condiciones[1] = htmlspecialchars($_GET["usuario"]);
    $lc_condiciones[2] = 0;
    $lc_condiciones[3] = 0;
    $lc_condiciones[4] = '';
    $lc_condiciones[5] = 0;
    $lc_condiciones[6] = $lc_rest;
    print $lc_apertura->fn_ejecutar("actualizaCajero", $lc_condiciones);

} else if (htmlspecialchars(isset($_GET["actualizaCajeroMotivo"]))) {
    $lc_condiciones[0] = 'UM';
    $lc_condiciones[1] = htmlspecialchars($_GET["usuario"]);
    $lc_condiciones[2] = 0;
    $lc_condiciones[3] = 0;
    $lc_condiciones[4] = strtoupper(utf8_decode($_GET["motivoDescuadre"]));
    $lc_condiciones[5] = htmlspecialchars($_GET["ctrcID"]);
    $lc_condiciones[6] = $lc_rest;
    print $lc_apertura->fn_ejecutar("actualizaCajeroMotivo", $lc_condiciones);

} else if (htmlspecialchars(isset($_GET["finDia"]))) {
    $lc_condiciones[0] = 'FD';
    $lc_condiciones[1] = $lc_usuarioId;
    $lc_condiciones[2] = $lc_rest;
    print $lc_apertura->fn_ejecutar("finDia", $lc_condiciones);

} else if (htmlspecialchars(isset($_GET["ValidaDescuadre"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["cadena"]);
    $lc_condiciones[1] = htmlspecialchars($_GET["cantidad"]);
    $lc_condiciones[2] = $lc_usuarioId;
    print $lc_apertura->fn_ejecutar("ValidaDescuadre", $lc_condiciones);

} else if (htmlspecialchars(isset($_GET["ValidaRegreso"]))) {
    $lc_condiciones[0] = $lc_rest;
    $lc_condiciones[1] = $lc_usuarioId;
    print $lc_apertura->fn_ejecutar("ValidaRegreso", $lc_condiciones);

} else if (htmlspecialchars(isset($_GET["validarCreencialesUsuario"]))) {
    $lc_condiciones[0] = 'A';
    $lc_condiciones[1] = $lc_rest;
    $lc_condiciones[2] = $lc_usuarioId;
    $lc_condiciones[3] = htmlspecialchars($_GET["usr_clave"]);
    $lc_condiciones[4] = htmlspecialchars($_GET["usr_tarjeta"]);
    $lc_condiciones[5] = htmlspecialchars($_GET["movimiento"]);
    $lc_condiciones[6] = htmlspecialchars($_GET["factura"]);
    print $lc_apertura->fn_ejecutar("validarCreencialesUsuario", $lc_condiciones);

} else if (htmlspecialchars(isset($_GET["validarCreencialesUsuariofactura"]))) {
    $lc_condiciones[0] = 'F';
    $lc_condiciones[1] = $lc_rest;
    $lc_condiciones[2] = $lc_usuarioId;
    $lc_condiciones[3] = htmlspecialchars($_GET["usr_clave"]);
    $lc_condiciones[4] = htmlspecialchars($_GET["usr_tarjeta"]);
    $lc_condiciones[5] = htmlspecialchars($_GET["movimiento"]);
    $lc_condiciones[6] = htmlspecialchars($_GET["factura"]);
    print $lc_apertura->fn_ejecutar("validarCreencialesUsuario", $lc_condiciones);

} else if (htmlspecialchars(isset($_GET["ValidaFondoRetirado"]))) {
    $lc_condiciones[0] = 'V';
    $lc_condiciones[1] = $ip;
    $lc_condiciones[2] = htmlspecialchars($_GET["usr_claveAdmin"]); //utilizamos esta variable para enviar el id de usuario
    $lc_condiciones[3] = '0';
    print $lc_apertura->fn_ejecutar("ValidaFondoRetirado", $lc_condiciones);

} else if (htmlspecialchars(isset($_GET["InsertcanalmovimientoFindelDia"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["periodo"]);
    $lc_condiciones[1] = htmlspecialchars($_GET["estacion"]);
    $lc_condiciones[2] = $lc_usuarioIdAdmin;
    print $lc_apertura->fn_ejecutar("InsertcanalmovimientoFindelDia", $lc_condiciones);

} else if (htmlspecialchars(isset($_GET["validaFindeDia"]))) {
    $lc_condiciones[0] = 'V';
    $lc_condiciones[1] = $lc_usuarioId;
    $lc_condiciones[2] = $lc_rest;
    print $lc_apertura->fn_ejecutar("validaFindeDia", $lc_condiciones);

} else if (htmlspecialchars(isset($_GET["valida24Horas"]))) {
    print $lc_apertura->valida24Horas($lc_rest,htmlspecialchars($_GET["fechaAperturaPeriodo"]));

} else if (htmlspecialchars(isset($_GET["cambiarEstadoEstaciones"]))) {
    print $lc_apertura->cambiarEstadoEstaciones($lc_rest,htmlspecialchars($_GET["fechaAperturaPeriodo"]));
} else if (htmlspecialchars(isset($_GET["validarCuentasAbiertas"]))) {
    $accion = 3;
    $id_restaurante = $lc_rest;
    $id_usuario = $lc_usuarioId;
    
    print $lc_apertura->validarCuentasAbiertas($accion, $id_restaurante, $id_usuario);
} else if (htmlspecialchars(isset($_GET["validarMotorizadoAsignados"]))) {
    $idRestaurante = $lc_rest;
    print $lc_apertura->validarMotorizadoAsignados( $idCadena, $idRestaurante, $idPeriodo );
} else if (htmlspecialchars(isset($_GET["validarPendientesApp"]))) {
    $idRestaurante = $lc_rest;
    print $lc_apertura->validarPendientesApp( $idCadena, $idRestaurante, $idPeriodo );
}else if (htmlspecialchars(isset($_GET["pedidosMotorizado"]))) {
    $lc_condiciones = htmlspecialchars($_GET["data"]);
    print $lc_apertura->fn_consultar("pedidosMotorizado", $lc_condiciones);
}else if (htmlspecialchars(isset($_POST["actualizarComandasMotorizados"]))) {
    $lc_condiciones = htmlspecialchars($_POST["data"]);
    print $lc_apertura->actualizarComandasMotorizados($lc_condiciones);
}else if (htmlspecialchars(isset($_POST["añadirComandasMotorizados"]))) {
    $lc_condiciones = htmlspecialchars($_POST["data"]);
    print $lc_apertura->añadirComandasMotorizados($lc_condiciones);
} 

else if (htmlspecialchars(isset($_GET["validarPendienteKiUP"]))) {
    $idRestaurante = $lc_rest;
    print $lc_apertura->App_pedido_pickup_actividad();
}

else if (htmlspecialchars(isset($_GET["validaDesasignarCajero"]))) {
    $lc_condiciones[0] = $idCadena;
    $lc_condiciones[1] = $lc_rest;
    $lc_condiciones[2] = htmlspecialchars($_GET["id_usuario"]);
    print $lc_apertura->fn_consultar("validaDesasignarCajero", $lc_condiciones);
}
else if (isset($_GET["consultaEstadoOrdenPedido"])) {
    $lc_condiciones[0] = 'E';
    $lc_condiciones[1] = $_GET["codigoOrden"];   
    $lc_condiciones[2] = $lc_rest; 
    print $lc_apertura->fn_consultar("consultaEstadoOrdenPedido", $lc_condiciones);
}else if (isset($_GET["anularMesaOdp"])) {
    $lc_condiciones[0] = '1';
    $lc_condiciones[1] = $_GET["codigoOrden"];
    $lc_condiciones[2] = '0';
    $lc_condiciones[3] = $lc_usuarioIdAdmin;
    $lc_condiciones[4] = '0';
    $lc_condiciones[5] = '0';   
$lc_condiciones[6] = $_GET["cuenta"];
    print $lc_apertura->fn_ejecutar("anularMesaOdp", $lc_condiciones);
}else if (isset($_GET["anularCuentaPorFacturar"])) {
    $lc_condiciones[0] = '3';
    $lc_condiciones[1] = $_GET["codigoOrden"];
    $lc_condiciones[2] = '0';
    $lc_condiciones[3] = $lc_usuarioIdAdmin;
    $lc_condiciones[4] = '0';   
    $lc_condiciones[5] = $_GET["codigoFactura"];
$lc_condiciones[6] = $_GET["cuenta"];
    print $lc_apertura->fn_ejecutar("anularCuentaPorFacturar", $lc_condiciones);
}else if (isset($_GET["actualizaEstacionOdp"])) {
    $lc_condiciones[0] = $_GET["accion"];
    $lc_condiciones[1] = $_GET["codigoOrden"];
    $lc_condiciones[2] = $lc_est_id;
    $lc_condiciones[3] = $lc_usuarioId;
    $lc_condiciones[4] = '0';   
    $lc_condiciones[5] = '0';
    $lc_condiciones[6] = '0';
    print $lc_apertura->fn_ejecutar("actualizaEstacionOdp", $lc_condiciones);
}elseif( isset( $_POST["IDCabeceraOrdenPedidoCFOP"] ) ) {
    $IDCabeceraOrdenPedido = $_POST["IDCabeceraOrdenPedidoCFOP"];

    print $lc_apertura->condicionFacturacionOrdenPedido( $IDCabeceraOrdenPedido );
}else if (isset($_GET["validaRetomaOrdenCualquierEstacion"])) {
    $lc_condiciones[0] = $lc_rest;   
    print $lc_apertura->fn_consultar("validaRetomaOrdenCualquierEstacion", $lc_condiciones);
}else if (isset($_POST["ejecutarCambioIva"])){
    $lc_condiciones[0] = $lc_rest;   
    print $lc_apertura->fn_consultar("ejecutarCambioIva", $lc_condiciones);
}