<?php

//////////////////////////////////////////////////////////////
////////DESARROLLADO POR: Worman Andrade//////////////////////
////////DESCRIPCION: Clase para Carga de Combos///////////////
/////////////////////En las Mesas/////////////////////////////
///////TABLAS INVOLUCRADAS: Cadena/////////////////////////// 
///////FECHA CREACION: 16-Enero-2014//////////////////////////
//////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////// 

include("../../system/conexion/clase_sql.php");
include("../../clases/clase_reservas.php");
 
$obj_area = new reservas();

//cargarcadena
//print $selects->fn_consultar('cargarcadena','');

 if(isset($_GET["cargarPiso"]))
 {  
 	$lc_datos[0] = $_GET["codigo"];
  	print $obj_area->fn_consultar("cargarPiso",$lc_datos);    
 }
 
if(isset($_GET["CargarArea"]))
 {  
  $lc_condiciones[0]=$_GET["codigo"];
  print $obj_area->fn_consultar("CargarArea",$lc_condiciones);    
 }
 
 if(isset($_GET["CargarMesa"]))
 {  
  $lc_condiciones[0]=$_GET["rest"];
  $lc_condiciones[1]=$_GET["piso"];
  $lc_condiciones[2]=$_GET["area"];
  
  print $obj_area->fn_consultar("CargarMesa",$lc_condiciones);    
 }
 

///Auto Completar
if(isset($_GET["autoCompletar"]))
{
	$lc_condiciones[0]=$_GET["term"];
	print $obj_area->fn_consultarCliente($lc_condiciones);
}

?>