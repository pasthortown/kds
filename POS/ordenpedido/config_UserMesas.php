<?php

//////////////////////////////////////////////////////////////
////////DESARROLLADO POR: Worman Andrade//////////////////////
////////DESCRIPCION: Clase para Carga de Combos///////////////
/////////////////////En las Mesas/////////////////////////////
///////TABLAS INVOLUCRADAS: Cadena////////////////////////////
///////FECHA CREACION: 16-Enero-2014//////////////////////////
//////////////////////////////////////////////////////////////
////////MODIFICADO: Jorge Tinoco /////////////////////////////
////////FECHA: 16-07-2014 ////////////////////////////////////
////////DESCRIPCION: Incluir archivo clase_userMesas.php /////
//////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////// 
////////////////////////////////////////////////////////////// 
////////MODIFICADO: Mychael Castro /////////////////////////////
////////FECHA: 18:20 11/5/2017 ////////////////////////////////////
////////DESCRIPCION: permisos segun perfiles de usuarios /////
//////////////////////////////////////////////////////////////

include"../system/conexion/clase_sql.php";
include"../clases/clase_userMesas.php";

$obj_area = new mesas();

//cargarcadena
//print $selects->fn_consultar('cargarcadena','');
if (htmlspecialchars(isset($_POST["cargarPermisos"]))) {
    $lc_datos[0] = htmlspecialchars($_POST["usuario_id"]);
    $lc_datos[1] = htmlspecialchars($_POST["pantalla"]);
	print $obj_area->fn_consultar("cargarPermisos", $lc_datos);
	
} else if (htmlspecialchars(isset($_GET["cargarPiso"]))) {
    $lc_datos[0] = htmlspecialchars($_GET["codigo"]);
    $lc_datos[1] = htmlspecialchars($_GET["est_id"]);
    $lc_datos[2] = htmlspecialchars($_GET["cnd_id"]);
	print $obj_area->fn_consultar("cargarPiso", $lc_datos);
	
} else if (htmlspecialchars(isset($_GET["CargarArea"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["codigo"]);
    $lc_condiciones[1] = htmlspecialchars($_GET["est_id"]);
    $lc_condiciones[2] = htmlspecialchars($_GET["cnd_id"]);
	print $obj_area->fn_consultar("CargarArea", $lc_condiciones);
	
} else if (htmlspecialchars(isset($_GET['CargarMesa']))) {
    $lc_condiciones[0] = htmlspecialchars($_GET['rest']);
    $lc_condiciones[1] = htmlspecialchars($_GET['piso']);
    $lc_condiciones[2] = htmlspecialchars($_GET['area']);
    $lc_condiciones[3] = htmlspecialchars($_GET['user_pos']);
    $lc_condiciones[4] = htmlspecialchars($_GET['est_id']);
    $lc_condiciones[5] = htmlspecialchars($_GET['idPeriodo']);
	print $obj_area->fn_consultar('CargarMesa', $lc_condiciones);
	
} else if (htmlspecialchars(isset($_GET["cargarEstadoMesas"]))) {
    $lc_datos[0] = htmlspecialchars($_GET["mesa_id"]);
	$lc_datos[1] = htmlspecialchars($_GET["usr_id"]);
    print $obj_area->fn_consultar("CargarEstadoMesas", $lc_datos);

/* ------------------------------------------------------------------------------------------------------
  retorna el id de la factura
  ------------------------------------------------------------------------------------------------------- */
} else if (htmlspecialchars(isset($_GET["retomarCuentaAbierta"]))) {
    $lc_datos[0] = htmlspecialchars($_GET["mesa_id"]);
    $lc_datos[1] = htmlspecialchars($_GET["rest_id"]);
	print $obj_area->fn_consultar("retomarCuentaAbierta", $lc_datos);

} else if (htmlspecialchars(isset($_POST["pedidoRapido"]))) {
    $lc_datos[0] = htmlspecialchars($_POST["est_id"]);
    $lc_datos[1] = htmlspecialchars($_POST["cdn_id"]);
	print $obj_area->fn_consultar("pedidoRapido", $lc_datos);

} else if (htmlspecialchars(isset($_POST["obtiene_url"]))) {
    $lc_datos[0] = htmlspecialchars($_POST["mesa_id"]);
    $lc_datos[1] = htmlspecialchars($_POST["odp_id"]);
	print $obj_area->fn_consultar("obtiene_url", $lc_datos);

} else if (htmlspecialchars(isset($_POST["VerificarMisMesa"]))) {
    $lc_datos[0] = htmlspecialchars($_POST["mesa_id"]);
    $lc_datos[1] = htmlspecialchars($_POST["periodo_id"]);
    $lc_datos[2] = htmlspecialchars($_POST["estacion_id"]);
    $lc_datos[3] = htmlspecialchars($_POST["user_login"]);
	print $obj_area->fn_consultar("VerificarMisMesa", $lc_datos);

} else if(htmlspecialchars(isset($_POST["retomaFacturaPendiente"]))) {
    $lc_datos[0]= htmlspecialchars($_POST["opcion"]); // opcion uno es para verificar si existe una factura pendiente
    $lc_datos[1]= htmlspecialchars($_POST["mesa_id"]);
	print $obj_area->fn_consultar("retomaFacturaPendiente", $lc_datos);

} else if(htmlspecialchars(isset($_POST["estadoMesa"]))) {
    $lc_datos[0]= htmlspecialchars($_POST["IDMesa"]);  
	print $obj_area->fn_consultar("estadoMesa", $lc_datos);

} else if(htmlspecialchars(isset($_POST["pedidoRapidoEnEspera"]))) {
    $lc_datos[0]= htmlspecialchars($_POST["rst_id"]);  
    $lc_datos[1]= htmlspecialchars($_POST["est_id"]);  
	print $obj_area->fn_consultar("pedidoRapidoEnEspera", $lc_datos);

} else if(htmlspecialchars(isset($_POST["verificaSeleccionNumeroMesa"]))) {
    $lc_datos[0]= htmlspecialchars($_POST["rst_id"]);  
	print $obj_area->fn_consultar("verificaSeleccionNumeroMesa", $lc_datos);

} else if(htmlspecialchars(isset($_POST["ResfrescarPanelMesas"]))) {
    $lc_datos[0]= htmlspecialchars($_POST["rst_id"]);  
	print $obj_area->fn_consultar("ResfrescarPanelMesas", $lc_datos);

} else if (htmlspecialchars(isset($_GET["TransferenciaCuentas"]))) {
    $lc_datos[0]= htmlspecialchars($_GET["opcion"]);
    $lc_datos[1]= htmlspecialchars($_GET["IDEstacion"]);
    $lc_datos[2]= htmlspecialchars($_GET["IDUsersPos"]);
    $lc_datos[3]= htmlspecialchars($_GET["IDPeriodo"]);
    $lc_datos[4]= htmlspecialchars($_GET["cdn_id"]);
	print $obj_area->fn_consultar("TransferenciaCuentas", $lc_datos);
	
} else if (htmlspecialchars(isset($_GET["obtenerUsuarios"]))) {
    $lc_datos[0]= htmlspecialchars($_GET["opcion"]);
    $lc_datos[1]= '';
    $lc_datos[2]= htmlspecialchars($_GET["IDUsersPos"]);
    $lc_datos[3]= '';
    $lc_datos[4]= 0;
	print $obj_area->fn_consultar("obtenerUsuarios", $lc_datos);

} else if(htmlspecialchars(isset($_GET["ActualizaTransferenciaCuentas"]))) {
    $lc_datos[0]= htmlspecialchars($_GET["opcion"]);
    $lc_datos[1]= htmlspecialchars($_GET["IDUsersPos"]);
    $lc_datos[2]= htmlspecialchars($_GET["IDEstacion"]);
	$lc_datos[3]= htmlspecialchars($_GET["IDCabeceraOrdenPedido"]);
	print $obj_area->fn_consultar("ActualizaTransferenciaCuentas", $lc_datos);

} else if (htmlspecialchars(isset($_POST["fidelizacionActiva"]))) {
    $idRestaurante = htmlspecialchars($_POST["idRestaurante"]);  
	print $obj_area->fidelizacionActiva($idRestaurante);

} else if (isset($_POST["actualizaTodasOdp"])) {
    $lc_datos[0]=$_POST["opcion"];
    $lc_datos[1]=$_POST["id_odp"];
    $lc_datos[2]=$_POST["estacion"];
    $lc_datos[3]=$_POST["usuario"];
	$lc_datos[4]=$_POST["periodo"];
    $lc_datos[5]='0';
$lc_datos[6]='0';
	print $obj_area->fn_consultar("actualizaTodasOdp", $lc_datos);

}