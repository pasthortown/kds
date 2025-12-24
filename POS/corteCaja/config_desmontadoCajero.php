<?php

session_start();
//include ("../system/seguridad.inc");
/////////////////////////////////////////////////////////////////////////////////
///////DESARROLLADO POR: JOSE FERNANDEZ//////////////////////////////////////////
///////DESCRIPCION	   : Archivo de configuracion del Modulo desmontar cajero////
////////TABLAS		   : ARQUEO_CAJA,BILLETE_ESTACION,///////////////////////////
//////////////////////////////CONTROL_ESTACION,ESTACION//////////////////////////
//////////////////////////////BILLETE_DENOMINACION///////////////////////////////
////////FECHA CREACION : 20/12/2013//////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////

include_once "../system/conexion/clase_sql.php";
include_once "../clases/clase_desmontadoCajero.php";
include_once ("../clases/clase_adminDesmontarCajero.php");

$lc_apertura = new desmontaCaja();
$lc_config= new desmontarCajero;
$lc_rest = $_SESSION['rstId'];
$lc_ip = $_SESSION['direccionIp'];
$lc_usuarioId = $_SESSION['usuarioId'];
$lc_usuarioIdAdmin = isset($_SESSION['usuarioIdAdmin'])?$_SESSION['usuarioIdAdmin']:null;
$lc_cadena = isset($_SESSION['cadenaId'])?$_SESSION['cadenaId']:null;
$lc_est_id = isset($_SESSION['estacionId'])?$_SESSION['estacionId']:null;
$lc_IDControlEstacion = isset($_SESSION['IDControlEstacion'])?$_SESSION['IDControlEstacion']:null;

if (htmlspecialchars(isset($_GET["consultaformaPago"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["accion"]);
    $lc_condiciones[1] = $lc_rest;
    $lc_condiciones[2] = $lc_ip;
    $lc_condiciones[3] = $lc_usuarioId;
    $lc_condiciones[4] = htmlspecialchars($_GET["banderaDesmontado"]);
    
    print $lc_apertura->fn_consultar("consultaformaPago", $lc_condiciones);
    /* $cajon = @fopen("LPT1", "w");  		
      fwrite($cajon, chr(27). chr(112). chr(0). chr(25). chr(250));//ABRIR EL CAJON
      fclose($cajon);
      $salida = shell_exec('lpr LPT1'); */
}

else if (htmlspecialchars(isset($_GET["consultaformaPagoDesmontadoDirecto"]))) {
    $lc_condiciones[0] = $lc_rest;
    $lc_condiciones[1] = $lc_ip; //$_GET["codigoUsuario"];
    
    print $lc_apertura->fn_consultar("consultaformaPagoDesmontadoDirecto", $lc_condiciones);
}

else if (htmlspecialchars(isset($_GET["consultaformaPagoModificado"]))) {
    $lc_condiciones[0] = $lc_ip; //$_GET["id_Estacion"];
    $lc_condiciones[1] = $lc_usuarioId; //$_GET["id_usuariO"];//
    $lc_condiciones[2] = htmlspecialchars($_GET["accion"]);
    $lc_condiciones[3] = $lc_usuarioIdAdmin;
    $lc_condiciones[4] = htmlspecialchars($_GET["formaPago"]);
    $lc_condiciones[5] = htmlspecialchars($_GET["estadoRetiro"]);
    $lc_condiciones[6] = $lc_IDControlEstacion;

    print $lc_apertura->fn_consultar("consultaformaPagoModificado", $lc_condiciones);
}

else if (htmlspecialchars(isset($_GET["calculatotalesModificados"]))) {
    $lc_condiciones[0] = $lc_ip; //$_GET["idestacion"];	
    $lc_condiciones[1] = $lc_usuarioId; //$_GET["userId"];	//
    $lc_condiciones[2] = htmlspecialchars($_GET["accion"]);
    $lc_condiciones[3] = $lc_usuarioIdAdmin;
    $lc_condiciones[4] = htmlspecialchars($_GET["estadoRetiro"]);
    $lc_condiciones[5] = $lc_IDControlEstacion;
    
    print $lc_apertura->fn_consultar("calculatotalesModificados", $lc_condiciones);
}

else if (htmlspecialchars(isset($_GET["DesmontadoDirecto"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["accion"]);
    $lc_condiciones[1] = $lc_usuarioId;
    $lc_condiciones[2] = $lc_rest;
    $lc_condiciones[3] = $lc_usuarioIdAdmin;
    
    print $lc_apertura->fn_ejecutar("DesmontadoDirecto", $lc_condiciones);
}

else if (htmlspecialchars(isset($_GET["consultatotalEstacion"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["accion"]);
    $lc_condiciones[1] = $lc_ip;
    
    print $lc_apertura->fn_consultar("consultatotalEstacion", $lc_condiciones);
}

else if (htmlspecialchars(isset($_GET["consultaidBilletes"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["accion"]);
    $lc_condiciones[1] = 0;
    $lc_condiciones[2] = htmlspecialchars($_GET["top"]);
    $lc_condiciones[3] = $lc_IDControlEstacion;
    
    print $lc_apertura->fn_consultar("consultaidBilletes", $lc_condiciones);
}

else if (htmlspecialchars(isset($_GET["consultatotalformaPago"]))) {
    $lc_condiciones[0] = $lc_ip; //$_GET["codigo"];	
    $lc_condiciones[1] = htmlspecialchars($_GET["idforma"]);
    $lc_condiciones[2] = $lc_rest;
    
    print $lc_apertura->fn_consultar("consultatotalformaPago", $lc_condiciones);
}

else if (htmlspecialchars(isset($_GET["consultaformaPagoModificadoTarjeta"]))) {
    $lc_condiciones[0] = $lc_ip; //$_GET["estaciond"];
    $lc_condiciones[1] = $lc_usuarioId; //$_GET["id_User"];
    $lc_condiciones[2] = $lc_usuarioIdAdmin;
    $lc_condiciones[3] = htmlspecialchars($_GET["formaPago"]);
    $lc_condiciones[4] = htmlspecialchars($_GET["estadoRetiro"]);
    $lc_condiciones[5] = $lc_cadena;
    $lc_condiciones[6] = $lc_IDControlEstacion;
    
    print $lc_apertura->fn_consultar("consultaformaPagoModificadoTarjeta", $lc_condiciones);
}

else if (htmlspecialchars(isset($_GET["consultaBilletes"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["accion"]);
    $lc_condiciones[1] = $lc_ip;
    $lc_condiciones[2] = 0;
    $lc_condiciones[3] = $lc_IDControlEstacion;
    
    print $lc_apertura->fn_consultar("consultaBilletes", $lc_condiciones);
}

else if (htmlspecialchars(isset($_GET["consultaBilletesModificados"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["accion"]);
    $lc_condiciones[1] = $lc_ip;
    $lc_condiciones[2] = 0;
    $lc_condiciones[3] = $lc_IDControlEstacion;
    
    print $lc_apertura->fn_consultar("consultaBilletesModificados", $lc_condiciones);
}

else if (htmlspecialchars(isset($_GET["totalesPos"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["accion"]);
    $lc_condiciones[1] = $lc_usuarioId;
    
    print $lc_apertura->fn_consultar("totalesPos", $lc_condiciones);
}

else if (htmlspecialchars(isset($_GET["totalesIngresados"]))) {
    $lc_condiciones[0] = $lc_usuarioId; //$_GET["User"];//
    $lc_condiciones[1] = htmlspecialchars($_GET["accion"]);
    $lc_condiciones[2] = $lc_est_id;
    $lc_condiciones[3] = htmlspecialchars($_GET["banderaDesmontado"]);
    
    print $lc_apertura->fn_consultar("totalesIngresados", $lc_condiciones);
}

else if (htmlspecialchars(isset($_POST["grabaBilletes"]))) {
    //$lc_condiciones6=$_GET["resNuevo"];//
    $lc_condiciones5 = ($_POST["cantidades2"]); //cantidad de billetes
    $lc_condiciones = ($_POST["resultado2"]); //valor total de billetes
    $lc_condiciones3 = ($_POST["oculto"]); //id de billete
    $lc_condiciones4 = ($_POST["oculto2"]); //id de control estacion
    //$lc_condiciones6=$_GET["oculto3"];//id de usuario
    for ($i = 0; $i < count($lc_condiciones5); $i++) {
        $lc_condiciones2[0] = htmlspecialchars($_POST["accion"]);
        $lc_condiciones2[1] = $lc_condiciones[$i];
        $lc_condiciones2[2] = $lc_condiciones3[$i]; //id de billete
        $lc_condiciones2[3] = $lc_condiciones4[$i];
        $lc_condiciones2[4] = $lc_condiciones5[$i];
        $lc_condiciones2[5] = $lc_usuarioIdAdmin;
        $lc_condiciones2[6] = htmlspecialchars($_POST["tipoEfectivo"]);
        $lc_condiciones2[7] = htmlspecialchars($_POST["banderaDesmontado"]);
        
        print $lc_apertura->fn_ejecutar("grabaBilletes", $lc_condiciones2);
    }
}

else if (htmlspecialchars(isset($_POST["grabaArqueo"]))) {
    $lc_condiciones[0] = htmlspecialchars($_POST["accion"]);
    $lc_condiciones[1] = $lc_usuarioId;
    $lc_condiciones[2] = $lc_rest;
    $lc_condiciones[3] = utf8_decode(htmlspecialchars($_POST["formaPago"]));
    $lc_condiciones[4] = htmlspecialchars($_POST["resNuevo"]);
    $lc_condiciones[5] = htmlspecialchars($_POST["accion_int"]);
    $lc_condiciones[6] = 0;
    $lc_condiciones[7] = htmlspecialchars($_POST["posCalculadoValor"]);
    $lc_condiciones[8] = htmlspecialchars($_POST["totalRetirado"]);
    $lc_condiciones[9] = htmlspecialchars($_POST["diferencia"]);
    $lc_condiciones[10] = htmlspecialchars($_POST["transacciones"]);
    $lc_condiciones[11] = $lc_IDControlEstacion;
    $lc_condiciones[12] = htmlspecialchars($_POST["estadoRetiro"]);
    $lc_condiciones[13] = htmlspecialchars($_POST["estadoPendiente"]);
    $lc_condiciones[14] = htmlspecialchars($_POST["arqueoTransacciones"]);
    $lc_condiciones[15] = htmlspecialchars($_POST["estadoSwitch"]);
    $lc_condiciones[16] = $lc_usuarioIdAdmin;

    print $lc_apertura->fn_ejecutar("grabaArqueo", $lc_condiciones);
}

else if (htmlspecialchars(isset($_GET["grabaarqueotarjeta"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["accion"]);
    $lc_condiciones[1] = $lc_usuarioId;
    $lc_condiciones[2] = $lc_rest;
    $lc_condiciones[3] = htmlspecialchars($_GET["idPago"]);
    $lc_condiciones[4] = htmlspecialchars($_GET["totalRetirado"]);
    $lc_condiciones[5] = htmlspecialchars($_GET["accion_int"]);
    $lc_condiciones[6] = htmlspecialchars($_GET["ingresoManualFormaPago"]);
    $lc_condiciones[7] = htmlspecialchars($_GET["posCalculadoValor"]);
    $lc_condiciones[8] = htmlspecialchars($_GET["totaltarjeta"]);
    $lc_condiciones[9] = htmlspecialchars($_GET["diferencia"]);
    $lc_condiciones[10] = htmlspecialchars($_GET["transacciones"]);
    $lc_condiciones[11] = $lc_IDControlEstacion;
    $lc_condiciones[12] = htmlspecialchars($_GET["estadoRetiro"]);
    $lc_condiciones[13] = htmlspecialchars($_GET["estadoPendiente"]);
    $lc_condiciones[14] = htmlspecialchars($_GET["ingresoTransacciones"]);
    $lc_condiciones[15] = htmlspecialchars($_GET["estadoSwitch"]);
    $lc_condiciones[16] = $lc_usuarioIdAdmin;

    print $lc_apertura->fn_ejecutar("grabaarqueotarjeta", $lc_condiciones);
}

else if (htmlspecialchars(isset($_GET["eliminaBilletes"]))) {
    $lc_condiciones = htmlspecialchars($_GET["eliminaBillete"]);
    for ($i = 0; $i < count($lc_condiciones); $i++) {
        $lc_condiciones2[0] = htmlspecialchars($_GET["accion"]);
        $lc_condiciones2[1] = $lc_condiciones[$i];
        $lc_condiciones2[2] = 0;
        $lc_condiciones2[3] = $lc_est_id;
        
        print $lc_apertura->fn_ejecutar("eliminaBilletes", $lc_condiciones2);
    }
}

else if (htmlspecialchars(isset($_GET["consultaTarjeta"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["accion"]);
    $lc_condiciones[1] = $lc_ip;
    $lc_condiciones[2] = htmlspecialchars($_GET["idPago"]);
    $lc_condiciones[3] = $lc_rest;
    $lc_condiciones[4] = $lc_usuarioId;
    $lc_condiciones[5] = htmlspecialchars($_GET["estadoRetiro"]);
    $lc_condiciones[6] = htmlspecialchars($_GET["estadoSwitch"]);

    print $lc_apertura->fn_consultar("consultaTarjeta", $lc_condiciones);
}

else if (htmlspecialchars(isset($_GET["eliminaArqueo"]))) {
    $lc_condiciones[0] = 'B';
    $lc_condiciones[1] = $lc_usuarioId;
    $lc_condiciones[2] = $lc_rest;
    $lc_condiciones[3] = 0;
    $lc_condiciones[4] = 0;
    $lc_condiciones[5] = 0;
    $lc_condiciones[6] = 0;
    $lc_condiciones[7] = 0;
    $lc_condiciones[8] = 0;
    $lc_condiciones[9] = 0;
    $lc_condiciones[10] = 0;
    $lc_condiciones[11] = $lc_IDControlEstacion;
    $lc_condiciones[12] = htmlspecialchars($_GET["estadoRetiro"]);
    $lc_condiciones[13] = 3;
    $lc_condiciones[14] = 0;
    $lc_condiciones[15] = 0;
    $lc_condiciones[16] = $lc_usuarioIdAdmin;
    
    print $lc_apertura->fn_ejecutar("eliminaArqueo", $lc_condiciones);
}

else if (htmlspecialchars(isset($_GET["actualizaCajero"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["accion"]);
    $lc_condiciones[1] = htmlspecialchars($_GET["usuario"]);
    $lc_condiciones[2] = $_SESSION['rstId'];
    $lc_condiciones[3] = $lc_usuarioId;
    $lc_condiciones[4] = $lc_IDControlEstacion;

    
    print $lc_apertura->fn_ejecutar("actualizaCajero", $lc_condiciones);
}

else if (htmlspecialchars(isset($_GET["actualizaCajeroMotivo"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["accion"]);
    $lc_condiciones[1] = $lc_usuarioIdAdmin;
    $lc_condiciones[2] = $lc_rest;
    $lc_condiciones[3] = strtoupper(utf8_decode(htmlspecialchars($_GET["motivoDescuadre"])));
    $lc_condiciones[4] = htmlspecialchars($_GET["accion_int"]);
    $lc_condiciones[5] = $lc_usuarioId;

    print $lc_apertura->fn_ejecutar("actualizaCajeroMotivo", $lc_condiciones);
}

else if (htmlspecialchars(isset($_GET["consultausuarioenEstacion"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["accion"]);
    $lc_condiciones[1] = $lc_ip; //$_GET["usuario"];
    $lc_condiciones[2] = $lc_usuarioId;

    print $lc_apertura->fn_consultar("consultausuarioenEstacion", $lc_condiciones);
}

else if (htmlspecialchars(isset($_GET["auditoriaCajero"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["accion"]);
    $lc_condiciones[1] = $lc_usuarioIdAdmin;
    $lc_condiciones[2] = $lc_rest;
    $lc_condiciones[3] = 0;
    $lc_condiciones[4] = htmlspecialchars($_GET["accion_int"]);
    $lc_condiciones[5] = $lc_usuarioId;
    
    print $lc_apertura->fn_ejecutar("auditoriaCajero", $lc_condiciones);
}

else if (htmlspecialchars(isset($_GET["auditoriaCajeroMotivo"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["accion"]);
    $lc_condiciones[1] = $lc_usuarioIdAdmin;
    $lc_condiciones[2] = $lc_rest;
    $lc_condiciones[3] = strtoupper(utf8_decode(htmlspecialchars($_GET["motivoDescuadre"])));
    $lc_condiciones[4] = htmlspecialchars($_GET["accion_int"]);
    $lc_condiciones[5] = $lc_usuarioId;
    
    print $lc_apertura->fn_ejecutar("auditoriaCajeroMotivo", $lc_condiciones);
}

else if (htmlspecialchars(isset($_GET["auditoriaEfectivo"]))) {
    $lc_condiciones10[0] = htmlspecialchars($_GET["accion"]);
    $lc_condiciones10[1] = $lc_usuarioIdAdmin;
    $lc_condiciones10[2] = $lc_rest;
    $lc_condiciones10[3] = htmlspecialchars($_GET["auditoriaTotal"]);

    print $lc_apertura->fn_ejecutar("auditoriaEfectivo", $lc_condiciones10);
}

else if (htmlspecialchars(isset($_GET["auditoriaEfectivoModificado"]))) {
    $lc_condiciones10[0] = htmlspecialchars($_GET["auditoriaTotal"]);
    $lc_condiciones10[4] = $lc_rest;
    $lc_condiciones10[5] = $lc_usuarioId;
    
    print $lc_apertura->fn_ejecutar("auditoriaEfectivoModificado", $lc_condiciones10);
}

else if (htmlspecialchars(isset($_GET["auditoriaTarjeta"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["accion"]);
    $lc_condiciones[1] = $lc_usuarioIdAdmin;
    $lc_condiciones[2] = $lc_rest;
    $lc_condiciones[3] = htmlspecialchars($_GET["totalTarjeta"]);
    $lc_condiciones[4] = htmlspecialchars($_GET["tipoTarjeta"]);
    $lc_condiciones[5] = htmlspecialchars($_GET["banderafp"]);

    print $lc_apertura->fn_ejecutar("auditoriaTarjeta", $lc_condiciones);
}

else if (htmlspecialchars(isset($_GET["auditoriaCancelar"]))) { /* $lc_condiciones[0]=$lc_rest;	
  $lc_condiciones[1]=$lc_usuarioId;
  $lc_condiciones[3]=$_GET["tipoTarjeta"];
  $lc_condiciones[4]=$_GET["totalTarjeta"]; */
    $lc_condiciones[2] = htmlspecialchars($_GET["total"]);
    $lc_condiciones[0] = $lc_rest;
    $lc_condiciones[1] = $lc_usuarioId;
    
    print $lc_apertura->fn_ejecutar("auditoriaCancelar", $lc_condiciones);
}

else if (htmlspecialchars(isset($_GET["validaMontoDescuadre"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["accion"]);
    $lc_condiciones[1] = $lc_cadena;
    $lc_condiciones[2] = htmlspecialchars($_GET["fpf_total_pagar"]);
    
    print $lc_apertura->fn_consultar("validaMontoDescuadre", $lc_condiciones);
}

else if (htmlspecialchars(isset($_GET["existeCuentaAbierta"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["accion"]);
    $lc_condiciones[1] = $lc_rest;
    $lc_condiciones[2] = $lc_usuarioId;
    
    print $lc_apertura->fn_consultar("existeCuentaAbierta", $lc_condiciones);
}

else if (htmlspecialchars(isset($_GET["existeCuentaAbiertaMesa"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["accion"]);
    $lc_condiciones[1] = $lc_rest;
    $lc_condiciones[2] = $lc_usuarioId;
    
    print $lc_apertura->fn_consultar("existeCuentaAbiertaMesa", $lc_condiciones);
}

else if (htmlspecialchars(isset($_GET["imprimeDesmontadoCajero"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["usr_id"]);
    $lc_condiciones[1] = htmlspecialchars($_GET["ctrc_id"]);
    $lc_condiciones[2] = $lc_usuarioIdAdmin;
    
    print $lc_apertura->fn_ejecutar("imprimeDesmontadoCajero", $lc_condiciones);
}

else if (htmlspecialchars(isset($_GET["consultaformaPagoEfectivo"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["accion"]);
    $lc_condiciones[1] = $lc_rest;
    $lc_condiciones[2] = $lc_ip;
    $lc_condiciones[3] = $lc_usuarioId;
    $lc_condiciones[4] = $lc_usuarioIdAdmin;
    
    print $lc_apertura->fn_consultar("consultaformaPagoEfectivo", $lc_condiciones);
}

else if (htmlspecialchars(isset($_GET["consultaValorRetiroEfectivo"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["accion"]);
    $lc_condiciones[1] = $lc_est_id;
    $lc_condiciones[2] = $lc_usuarioIdAdmin;
    
    print $lc_apertura->fn_consultar("consultaValorRetiroEfectivo", $lc_condiciones);
}

else if (htmlspecialchars(isset($_GET["auditoriaRetiroEfectivo"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["accion"]);
    $lc_condiciones[1] = $lc_rest;
    $lc_condiciones[2] = $lc_usuarioId;
    $lc_condiciones[3] = $lc_usuarioIdAdmin;
    $lc_condiciones[4] = $lc_est_id;
    $lc_condiciones[5] = htmlspecialchars($_GET["valor_retiro_efectivo"]);

    print $lc_apertura->fn_ejecutar("auditoriaRetiroEfectivo", $lc_condiciones);
}

else if (htmlspecialchars(isset($_POST["grabaBilletesMod"]))) {
    $lc_condiciones[0] = htmlspecialchars($_POST["accion"]);

    if (htmlspecialchars(isset($_POST["id_billetes"]))) {
        $lc_condiciones[1] = 0;
        foreach (htmlspecialchars($_POST['id_billetes']) as $lc_billetes) {
            $lc_condiciones[1] = $lc_condiciones[1] . ',' . $lc_billetes;
        }
    } else {
        $lc_condiciones[1] = 0;
    }
    
    if (htmlspecialchars(isset($_POST["ctr_estacion"]))) {
        $lc_condiciones[2] = 0;
        foreach (htmlspecialchars($_POST['ctr_estacion']) as $lc_ctrEstacion) {
            $lc_condiciones[2] = $lc_condiciones[2] . ',' . $lc_ctrEstacion;
        }
    } else {
        $lc_condiciones[2] = 0;
    }
    
    if (htmlspecialchars(isset($_POST["cantidades"]))) {
        $lc_condiciones[3] = 0;
        foreach (htmlspecialchars($_POST['cantidades']) as $lc_cantidades) {
            $lc_condiciones[3] = $lc_condiciones[3] . ',' . $lc_cantidades;
        }
    } else {
        $lc_condiciones[3] = 0;
    }
    
    if (htmlspecialchars(isset($_POST["total_billetes"]))) {
        $lc_condiciones[4] = 0;
        foreach (htmlspecialchars($_POST['total_billetes']) as $lc_totalBilletes) {
            $lc_condiciones[4] = $lc_condiciones[4] . ',' . $lc_totalBilletes;
        }
    } else {
        $lc_condiciones[4] = 0;
    }
    
    if (htmlspecialchars(isset($_POST["id_usuario"]))) {
        $lc_condiciones[5] = 0;
        foreach (htmlspecialchars($_POST['id_usuario']) as $lc_usuario) {
            $lc_condiciones[5] = $lc_condiciones[5] . ',' . $lc_usuario;
        }
    } else {
        $lc_condiciones[5] = 0;
    }

    $lc_condiciones[6] = htmlspecialchars($_POST["tipoEfectivo"]);
    
    print $lc_apertura->fn_ejecutar("grabaBilletesMod", $lc_condiciones);
}

else if (htmlspecialchars(isset($_GET["asentarRetiroEfectivo"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["accion"]);
    $lc_condiciones[1] = $lc_usuarioIdAdmin;
    $lc_condiciones[2] = htmlspecialchars($_GET["estado_asentado_refectivo"]);
    $lc_condiciones[3] = $lc_usuarioId;
    $lc_condiciones[4] = htmlspecialchars($_GET["efectivo_posCalculado"]);
    $lc_condiciones[5] = htmlspecialchars($_GET["valor_retiro_efectivo"]);
    $lc_condiciones[6] = htmlspecialchars($_GET["estadoRetiro"]);

    print $lc_apertura->fn_ejecutar("asentarRetiroEfectivo", $lc_condiciones);
}

else if (htmlspecialchars(isset($_GET["eliminaBilletesPendiente"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["accion"]);
    $lc_condiciones[1] = $lc_usuarioIdAdmin;
    $lc_condiciones[2] = htmlspecialchars($_GET["estado_pendiente_efectivo"]);
    $lc_condiciones[3] = $lc_usuarioId;
    $lc_condiciones[4] = 0;
    $lc_condiciones[5] = 0;
    $lc_condiciones[6] = htmlspecialchars($_GET["estadoRetiro"]);

    print $lc_apertura->fn_ejecutar("eliminaBilletesPendiente", $lc_condiciones);
}

else if (htmlspecialchars(isset($_GET["desasignarEnEstacion"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["accion"]);
    $lc_condiciones[1] = $lc_ip;
    $lc_condiciones[2] = $lc_rest;

    print $lc_apertura->fn_consultar("desasignarEnEstacion", $lc_condiciones);
}

else if (htmlspecialchars(isset($_GET["traeUsuarioAdmin"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["accion"]);
    $lc_condiciones[1] = $lc_usuarioIdAdmin;
    $lc_condiciones[2] = $lc_est_id;

    print $lc_apertura->fn_consultar("traeUsuarioAdmin", $lc_condiciones);
}

else if (htmlspecialchars(isset($_GET["traeMotivosDescuadre"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["accion"]);
    $lc_condiciones[1] = $lc_cadena;
    //$lc_condiciones[2]=	$lc_est_id;

    print $lc_apertura->fn_consultar("traeMotivosDescuadre", $lc_condiciones);
}

else if (htmlspecialchars(isset($_GET["validaDesasignarCajero"]))) {
    $lc_condiciones[0] = $lc_cadena;
    $lc_condiciones[1] = $lc_rest;
    $lc_condiciones[2] = $lc_usuarioId;
    print $lc_apertura->fn_consultar("validaDesasignarCajero", $lc_condiciones);
}

else if (htmlspecialchars(isset($_GET["retirofondo"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["accion"]);
    $lc_condiciones[1] = $lc_usuarioId;
    $lc_condiciones[2] = $lc_IDControlEstacion;
    $lc_condiciones[3] = $lc_cadena;
    $lc_condiciones[4] = $lc_rest;
    print $lc_apertura->fn_consultar("retirofondo", $lc_condiciones);
}

else if (htmlspecialchars(isset($_GET["cargarFormasPago"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["accion"]);
    $lc_condiciones[1] = $lc_est_id;
    $lc_condiciones[2] = $lc_rest;
    $lc_condiciones[3] = 0;

    print $lc_apertura->fn_consultar("cargarFormasPago", $lc_condiciones);
}

else if (htmlspecialchars(isset($_GET["grabaarqueoformapago"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["accion"]);
    $lc_condiciones[1] = $lc_usuarioId;
    $lc_condiciones[2] = $lc_rest;
    $lc_condiciones[3] = htmlspecialchars($_GET["fmp_id"]);
    $lc_condiciones[4] = htmlspecialchars($_GET["totalFormaPago"]);
    $lc_condiciones[5] = htmlspecialchars($_GET["accion_int"]);
    $lc_condiciones[6] = htmlspecialchars($_GET["banderafp"]);
    $lc_condiciones[7] = 0;
    $lc_condiciones[8] = 0;
    $lc_condiciones[9] = 0;
    $lc_condiciones[10] = 0;
    $lc_condiciones[11] = $lc_IDControlEstacion;
    $lc_condiciones[12] = htmlspecialchars($_GET["estadoRetiro"]);
    $lc_condiciones[13] = 0;
    $lc_condiciones[14] = 0;
    $lc_condiciones[15] = htmlspecialchars($_GET["estadoSwitch"]);
    $lc_condiciones[16] = $lc_usuarioIdAdmin;
    
    print $lc_apertura->fn_ejecutar("grabaarqueoformapago", $lc_condiciones);
}

else if (htmlspecialchars(isset($_POST["eliminaFormasPagoAgregadas"]))) {
    $lc_condiciones[0] = htmlspecialchars($_POST["accion"]);
    $lc_condiciones[1] = $lc_usuarioId;
    $lc_condiciones[2] = $lc_rest;
    $lc_condiciones[3] = 0;
    $lc_condiciones[4] = 0;
    $lc_condiciones[5] = 0;
    $lc_condiciones[6] = htmlspecialchars($_POST["banderafp"]);
    $lc_condiciones[7] = 0;
    $lc_condiciones[8] = 0;
    $lc_condiciones[9] = 0;
    $lc_condiciones[10] = 0;
    $lc_condiciones[11] = $lc_IDControlEstacion;
    $lc_condiciones[12] = htmlspecialchars($_POST["estadoRetiro"]);
    $lc_condiciones[13] = 0;
    $lc_condiciones[14] = 0;
    $lc_condiciones[15] = htmlspecialchars($_POST["estadoSwitch"]);
    $lc_condiciones[16] = $lc_usuarioIdAdmin;
    
    print $lc_apertura->fn_ejecutar("eliminaFormasPagoAgregadas", $lc_condiciones);
}

else if (htmlspecialchars(isset($_GET["validarUsuarioAdministrador"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["usr_Admin"]);
    $lc_condiciones[1] = $lc_rest;
    $lc_condiciones[2] = 'A';
    $lc_condiciones[3] = $lc_usuarioId;
    $lc_condiciones[4] = 0;
    
    print $lc_apertura->fn_consultar("validarUsuarioAdministrador", $lc_condiciones);
}

else if (htmlspecialchars(isset($_GET["traeValorFormaPago"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["accion"]);
    $lc_condiciones[1] = $lc_est_id;
    $lc_condiciones[2] = $lc_rest;
    $lc_condiciones[3] = htmlspecialchars($_GET["fmp_id"]);

    print $lc_apertura->fn_consultar("traeValorFormaPago", $lc_condiciones);
}

else if (htmlspecialchars(isset($_GET["consultaCupones"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["accion"]);
    $lc_condiciones[1] = $lc_rest;
    $lc_condiciones[2] = $lc_ip;
    $lc_condiciones[3] = $lc_usuarioId;
    $lc_condiciones[4] = 0;
    
    print $lc_apertura->fn_consultar("consultaCupones", $lc_condiciones);
}

else if (htmlspecialchars(isset($_GET["InsertcanalmovimientoCorteX"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["usr_id"]);
    $lc_condiciones[1] = htmlspecialchars($_GET["ctrc_id"]);
    $lc_condiciones[2] = $lc_usuarioIdAdmin;
    
    print $lc_apertura->fn_ejecutar("InsertcanalmovimientoCorteX", $lc_condiciones);
}

else if (htmlspecialchars(isset($_GET["canalMovimientoArqueo"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["usr_id"]);
    $lc_condiciones[1] = htmlspecialchars($_GET["ctrc_id"]);
    $lc_condiciones[2] = $lc_usuarioIdAdmin;
    print $lc_apertura->fn_canalMovimientoArqueo($lc_condiciones);
}

else if (htmlspecialchars(isset($_GET["realizaTransferencia"]))) { 
    $lc_condiciones[0] = $lc_rest;
    $lc_condiciones[1] = $lc_cadena;
    print $lc_apertura->fn_validaTransferencia($lc_condiciones);
}
else if (htmlspecialchars(isset($_GET["consultaValorTransferencia"]))) { 
    $lc_condiciones[0] = 1;
    $lc_condiciones[1] = $lc_rest;
    $lc_condiciones[2] = $lc_cadena;
    $lc_condiciones[3] = $lc_IDControlEstacion;
    print $lc_apertura->fn_consultaValorTransferencia($lc_condiciones);
}

else if (htmlspecialchars(isset($_GET["generaEgresoOrigen"]))) { 
    $lc_condiciones[0] = $lc_IDControlEstacion;
    $lc_condiciones[1] = $lc_usuarioId;
//    $lc_condiciones[2] = $lc_IDControlEstacion;
    print $lc_apertura->fn_generaEgresoTransferenciaOrigen($lc_condiciones);
    //echo("LIIIIISTOOO");
}
else if (htmlspecialchars(isset($_GET["reversarCCL"]))) { 
    $lc_condiciones[0] = $lc_IDControlEstacion;
    print $lc_apertura->fn_consultar("reversarCCL", $lc_condiciones);
}
else if (htmlspecialchars(isset($_GET["consultaFondoAsignado"]))) { 
    $lc_condiciones[0] = 'C';
    $lc_condiciones[1] = $lc_IDControlEstacion;
    print $lc_apertura->fn_consultaFondoAsignado($lc_condiciones);
}
else if (htmlspecialchars(isset($_GET["validaVentaInterface"]))) {
    $accion = $_GET["accion"]; 
    $id_periodo = $_GET["id_periodo"];
    $id_usuario = $lc_usuarioId;
    $id_control = $lc_IDControlEstacion;
    print $lc_apertura->validaInterfaceCuadreVenta($accion, $id_periodo, $id_usuario, $id_control);
}
else if (htmlspecialchars(isset($_GET["retiroCashless"]))) {
    $lc_condiciones[0] = $lc_usuarioId;
    $lc_condiciones[1] = $lc_IDControlEstacion;
    $lc_condiciones[2] = $lc_usuarioIdAdmin;
    print $lc_apertura->fn_ejecutar("retiroCashless", $lc_condiciones);
}
else if (htmlspecialchars(isset($_POST["eliminaFormasPagoAgregadasCashless"]))) {
    $lc_condiciones[0] = htmlspecialchars($_POST["accion"]);
    $lc_condiciones[1] = $lc_usuarioId;
    $lc_condiciones[2] = $lc_rest;
    $lc_condiciones[3] = 0;
    $lc_condiciones[4] = 0;
    $lc_condiciones[5] = 0;
    $lc_condiciones[6] = htmlspecialchars($_POST["banderafp"]);
    $lc_condiciones[7] = 0;
    $lc_condiciones[8] = 0;
    $lc_condiciones[9] = 0;
    $lc_condiciones[10] = 0;
    $lc_condiciones[11] = $lc_IDControlEstacion;
    $lc_condiciones[12] = htmlspecialchars($_POST["estadoRetiro"]);
    $lc_condiciones[13] = 0;
    $lc_condiciones[14] = 0;
    $lc_condiciones[15] = htmlspecialchars($_POST["estadoSwitch"]);
    $lc_condiciones[16] = $lc_usuarioIdAdmin;
    
    print $lc_apertura->fn_ejecutar("eliminaFormasPagoAgregadasCashless", $lc_condiciones);
}

