<?php
session_start();
include_once("../seguridades/seguridad.inc");
///////////////////////////////////////////////////////////////////////////////
///////DESARROLLADOR: Jorge Tinoco ////////////////////////////////////////////
///////DESCRIPCION: Pantalla de configuración de menú /////////////////////////
///////TABLAS INVOLUCRADAS: Menu, /////////////////////////////////////////////
///////FECHA CREACION: 20-02-2015 /////////////////////////////////////////////
///////FECHA ULTIMA MODIFICACION: 25/05/2015 //////////////////////////////////
///////USUARIO QUE MODIFICO: Daniel Llerena ///////////////////////////////////
///////DECRIPCION ULTIMO CAMBIO: Cambio de estilos ////////////////////////////
///////////////////////////////////////////////////////////////////////////////
	
	/*include_once("../../system/conexion/clase_sql.php");
	include_once("../../clases/clase_seguridades.php");
	include_once("../../clases/clase_menu.php");*/
	
	$lc_cadena = $_SESSION['cadenaId'];
	
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>


	<script src="../js/jquery1.11.1.js"></script>
    <script src="../bootstrap/js/bootstrap.js"></script>
    <script src="../js/jquery-ui.js"></script>
    <script language="javascript1.1"  src="../js/alertify.js"></script>
    <script type="text/javascript" src="../js/ajax_reportesrs.js"></script>    
</head>
<body>
<br><br>
<div align="center">
<input inputmode="none"  type="hidden" value="<?php echo $lc_cadena;?>" id="idcadena" />
<input inputmode="none"  type="button" onclick="fn_verreporte(1)" value="Ver Reporte" />
<input inputmode="none"  type="button" onclick="fn_verreporte(2)" value="Exportar a Excel" />
<input inputmode="none"  type="button" onclick="fn_verreporte(3)" value="Exportar a PDF" />
</div>
&nbsp;&nbsp;&nbsp;&nbsp;
<div id="respuesta" style="overflow:auto; width:900px; height:600px"></div>
</body>
</html>