<?php	
///////////////////////////////////////////////////////////
///////DESARROLLADO POR: Cristhian Castro ////////////////////
///////DESCRIPCION: ///////////////////
///////TABLAS INVOLUCRADAS: Menu_Agrupacion, ////////
////////////////Menu_Agrupacionproducto////////
////////////////Detalle_Orden_Pedido////////
///////////////////Plus, Precio_Plu, Mesas///////////////////
///////FECHA CREACION: 16-04-2014//////////////////////////
///////FECHA ULTIMA MODIFICACION: 07-05-2014////////////////
///////USUARIO QUE MODIFICO:  Cristhian Castro///////////////
///////DECRIPCION ULTIMO CAMBIO: Documentación y validación en Internet Explorer 9+///////
/////////////////////////////////////////////////////////// 
	
	session_start();
	
	include_once"../system/conexion/clase_sql.php";
	include_once"../clases/clase_seguridades.php";
	include_once"../clases/clase_ordenPedido.php";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Canales de Impresión</title>
    <link rel="stylesheet" href="../css/style_canalesImpresion.css" type="text/css" media="screen"/>
    <link rel="stylesheet" href="../css/style_canalesImpresion.css" type="text/css" media="print"/>
    
    <script src="../js/jquery.min.js"></script>
    <script type="text/javascript" src="../js/ajax_canalesImpresion.js"></script>
    <script type="text/javascript" src="../js/ajax.js"></script>

    <!--Scripts para alertas-->
	<link rel="stylesheet" href="../css/alertify.core.css" />
    <link rel="stylesheet" href="../css/alertify.default.css" />
  	<script type="text/javascript" src="../js/alertify.js"></script>     
   
</head>
<?php
	$odp_id = htmlspecialchars($_GET["orden"]);
	$cprn_id = htmlspecialchars($_GET["impresora"]);
?>
<body>
<input inputmode="none"  type="hidden" name="hide_odp_id" id="hide_odp_id" value="<?php echo $odp_id; ?>"/>
<input inputmode="none"  type="hidden" name="hide_cprn_id" id="hide_cprn_id" value="<?php echo $cprn_id; ?>"/>
<div align="center">
    <div id="impresionOrden">
        <div id="listadoCabecera"></div>
        <ul id="listadoPedido"></ul>
    </div>
</div>
</body>
</html>