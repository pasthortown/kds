
<?php
///////////////////////////////////////////////////////////////////////////////////
////////DESARROLLADO POR: CHRISTIAN PINTO//////////////////////////////////////////
///////////DESCRIPCION: PANTALLA DE COLECCIONES DE DATOS, CREAR MODIFICAR /////////
////////////////TABLAS: Colecciones Varias ////////////////////////////////////////
////////FECHA CREACION: 16/03/2016/////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////

include_once"../../system/conexion/clase_sql.php"; 
include_once "../../clases/clase_adminColeccionesDeDatos.php";
 
$lc_config   = new colecciones();	
$lc_cadena	 = $_SESSION['cadenaId'];
$lc_usuario = $_SESSION['usuarioId'];
$lc_restaurante = $_SESSION['rstId'];

if(htmlspecialchars(isset($_GET["cargarTablasCabeceracolecciones"]))){
	$lc_condiciones[0] = htmlspecialchars($_GET["accion"]);
	$lc_condiciones[1] = 0;
	$lc_condiciones[2] = $lc_cadena;
	$lc_condiciones[3] = 0;
	$lc_condiciones[4] = 0;
	$lc_condiciones[5] = 0;
	print $lc_config->fn_consultar("cargarTablasCabeceracolecciones",$lc_condiciones);
}
if(htmlspecialchars(isset($_GET["cargarTablaCabecera"]))){
	$lc_condiciones[0] = htmlspecialchars($_GET["accion"]);
	$lc_condiciones[1] = htmlspecialchars($_GET["nombretabla"]);
	$lc_condiciones[2] = $lc_cadena;
	$lc_condiciones[3] = 0;
	$lc_condiciones[4] = 0;
	$lc_condiciones[5] = 0;
	print $lc_config->fn_consultar("cargarTablaCabecera",$lc_condiciones);
}
if(htmlspecialchars(isset($_GET["guardarCabeceraColeccion"]))){
	$lc_condiciones[0] = htmlspecialchars($_GET["accion"]);
	$lc_condiciones[1] = htmlspecialchars($_GET["name_table"]);
	$lc_condiciones[2] = htmlspecialchars($_GET["descripcion"]);
	$lc_condiciones[3] = htmlspecialchars($_GET["idintegracion"]);
	$lc_condiciones[4] = htmlspecialchars($_GET["iddescripcion"]);
	$lc_condiciones[5] = htmlspecialchars($_GET["estatus1"]);
	$lc_condiciones[6] = htmlspecialchars($_GET["estatus2"]);
	$lc_condiciones[7] = htmlspecialchars($_GET["configuracion"]);
	$lc_condiciones[8] = htmlspecialchars($_GET["rconfiguracion"]);
	$lc_condiciones[9] = htmlspecialchars($_GET["reporte"]);
	$lc_condiciones[10] = htmlspecialchars($_GET["cubo"]);
	$lc_condiciones[11] = htmlspecialchars($_GET["estado"]);
	$lc_condiciones[12] = $lc_cadena;
	$lc_condiciones[13] = $lc_usuario;
	$lc_condiciones[14] = htmlspecialchars($_GET["resultado"]);
	$lc_condiciones[15] = htmlspecialchars($_GET["ID_Coleccion"]);
	$lc_condiciones[16] = 0;
	$lc_condiciones[17] = 0;
	$lc_condiciones[18] = 0;
	$lc_condiciones[19] = 0;
	print $lc_config->fn_ejecutar("guardarCabeceraColeccion",$lc_condiciones);
}

if(htmlspecialchars(isset($_GET["cargarNombreTablasColecciones"]))){
	$lc_condiciones[0] = htmlspecialchars($_GET["accion"]);
	$lc_condiciones[1] = htmlspecialchars($_GET["nombretabla"]);
	$lc_condiciones[2] = $lc_cadena;
	$lc_condiciones[3] = 0;
	$lc_condiciones[4] = 0;
	$lc_condiciones[5] = 0;
	print $lc_config->fn_consultar("cargarNombreTablasColecciones",$lc_condiciones);
}

if(htmlspecialchars(isset($_GET["cargarTablaColeccionDeDatos"]))){
	$lc_condiciones[0] = htmlspecialchars($_GET["accion"]);
	$lc_condiciones[1] = htmlspecialchars($_GET["nombretabla"]);
	$lc_condiciones[2] = $lc_cadena;
	$lc_condiciones[3] = 0;
	$lc_condiciones[4] = htmlspecialchars($_GET["ID_Coleccion"]);
	$lc_condiciones[5] = 0;
	print $lc_config->fn_consultar("cargarTablaColeccionDeDatos",$lc_condiciones);
}

if(htmlspecialchars(isset($_GET["cargarTablaColecciones"]))){
	$lc_condiciones[0] = htmlspecialchars($_GET["accion"]);
	$lc_condiciones[1] = htmlspecialchars($_GET["nombretabla"]);
	$lc_condiciones[2] = $lc_cadena;
	$lc_condiciones[3] = htmlspecialchars($_GET["nombre_tablaColeccionDeDatos"]);
	$lc_condiciones[4] = 0;
	$lc_condiciones[5] = 0;
	print $lc_config->fn_consultar("cargarTablaColecciones",$lc_condiciones);
}

if(htmlspecialchars(isset($_GET["traeTipoDeDato"]))){
	$lc_condiciones[0] = htmlspecialchars($_GET["accion"]);
	$lc_condiciones[1] = 0;
	$lc_condiciones[2] = $lc_cadena;
	$lc_condiciones[3] = htmlspecialchars($_GET["nombre_tablaColeccionDeDatos"]);
	$lc_condiciones[4] = 0;
	$lc_condiciones[5] = 0;
	print $lc_config->fn_consultar("traeTipoDeDato",$lc_condiciones);
}

if(htmlspecialchars(isset($_GET["traeColeccionesCabecera"]))){
	$lc_condiciones[0] = htmlspecialchars($_GET["accion"]);
	$lc_condiciones[1] = htmlspecialchars($_GET["nombretabla"]);
	$lc_condiciones[2] = $lc_cadena;
	$lc_condiciones[3] = 0;
	$lc_condiciones[4] = 0;
	$lc_condiciones[5] = 0;
	print $lc_config->fn_consultar("traeColeccionesCabecera",$lc_condiciones);
}

if(htmlspecialchars(isset($_GET["guardarColeccionDeDatos"]))){
	$lc_condiciones[0] = htmlspecialchars($_GET["accion"]);
	$lc_condiciones[1] = htmlspecialchars($_GET["name_table"]);
	$lc_condiciones[2] = htmlspecialchars($_GET["descripcion"]);
	$lc_condiciones[3] = htmlspecialchars($_GET["idintegracion"]);
	$lc_condiciones[4] = htmlspecialchars($_GET["iddescripcion"]);
	$lc_condiciones[5] = htmlspecialchars($_GET["estatus1"]);
	$lc_condiciones[6] = htmlspecialchars($_GET["estatus2"]);
	$lc_condiciones[7] = 0;
	$lc_condiciones[8] = 0;
	$lc_condiciones[9] = 0;
	$lc_condiciones[10] = 0;
	$lc_condiciones[11] = htmlspecialchars($_GET["estado"]);
	$lc_condiciones[12] = $lc_cadena;
	$lc_condiciones[13] = $lc_usuario;
	$lc_condiciones[14] = htmlspecialchars($_GET["resultado"]);
	$lc_condiciones[15] = htmlspecialchars($_GET["idcoleccion"]);
	$lc_condiciones[16] = htmlspecialchars($_GET["tipodedato"]);
	$lc_condiciones[17] = htmlspecialchars($_GET["obligatorioB"]);
	$lc_condiciones[18] = htmlspecialchars($_GET["especificarValorB"]);
	$lc_condiciones[19] = htmlspecialchars($_GET["ID_ColeccionDeDatos"]);
	
	print $lc_config->fn_ejecutar("guardarColeccionDeDatos",$lc_condiciones);
}

if(htmlspecialchars(isset($_GET["traeColeccionesModificar"]))){
	$lc_condiciones[0] = htmlspecialchars($_GET["accion"]);
	$lc_condiciones[1] = htmlspecialchars($_GET["nombretabla"]);
	$lc_condiciones[2] = $lc_cadena;
	$lc_condiciones[3] = 0;
	$lc_condiciones[4] = htmlspecialchars($_GET["ID_Coleccion"]);
	$lc_condiciones[5] = 0;
	print $lc_config->fn_consultar("traeColeccionesModificar",$lc_condiciones);
}

if(htmlspecialchars(isset($_GET["traeColeccionDeDatosModificar"]))){
	$lc_condiciones[0] = htmlspecialchars($_GET["accion"]);
	$lc_condiciones[1] = htmlspecialchars($_GET["nombretabla"]);
	$lc_condiciones[2] = $lc_cadena;
	$lc_condiciones[3] = htmlspecialchars($_GET["nombre_tablaColeccionDeDatos"]);
	$lc_condiciones[4] = htmlspecialchars($_GET["ID_Coleccion"]);
	$lc_condiciones[5] = htmlspecialchars($_GET["ID_ColeccionDeDatos"]);
	print $lc_config->fn_consultar("traeColeccionDeDatosModificar",$lc_condiciones);
}

?>