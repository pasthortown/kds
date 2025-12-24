<?php 
include_once("../system/conexion/clase_sql.php");
include_once("../clases/clase_recargaIngreso.php");

$idCadena = $_GET['idCadena'];
$idRestaurante = $_GET['idRestaurante'];
$idTienda = $_GET['idTienda'];
$idTransaccion = $_GET['idTransaccion'];

//$idCadena = 12;
//$idRestaurante = 555;
//$idTienda = 'V030';
//$idTransaccion = '0647800d-11e8-e611-80dc-9c8e990128c9';
$impresion = new Recargas();

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

	<?php
		$respuesta = $impresion->impresionRecarga($idCadena, $idRestaurante, $idTransaccion);
		print $respuesta["head"];
		print $respuesta["totales"];
		print $respuesta["firma"];
		print $respuesta["mensaje"];
	?>

</html>