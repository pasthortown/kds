<?php

//session_start();
///////////////////////////////////////////////////////////////////////////////
///////DESARROLLADOR: Christian Pinto /////////////////////////////////////////
///////DESCRIPCION: Pantalla de Formas Pago Subir Imagen //////////////////////
///////TABLAS INVOLUCRADAS: Formaspago, ///////////////////////////////////////
///////FECHA CREACION: 2-07-2015 /////////////////////////////////////////////
///////FECHA ULTIMA MODIFICACION: /////////////////////////////////////////////
///////USUARIO QUE MODIFICO: //////////////////////////////////////////////////
///////DECRIPCION ULTIMO CAMBIO: //////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////

include_once("../../system/conexion/clase_sql.php");

$lc_config = new sql();

$fmp_id = $_POST['codigofp'];
$imagen = $_POST['imagen'];

/*
  $arp_id = $_POST['arp_id'];
  $imagen = $_FILES['imagen']['tmp_name'];
  $tamanio = $_FILES["imagen"]["size"];
  $tipo = $_FILES["imagen"]["type"];
  $nombre = $_FILES["imagen"]["name"];
  $fp = fopen($imagen, "rb");
  $contenido = fread($fp, $tamanio);
  $contenido = addslashes($contenido);
  fclose($fp);
  $contenido = base64_encode($contenido);
 */
$lc_sql = "UPDATE Formapago SET fmp_imagen = '$imagen' WHERE fmp_id = " . $fmp_id;
//$lc_sql = "UPDATE AreaPiso SET arp_img = '$imagen', arp_name='$nombre', arp_type='$tipo', arp_size=$tamanio WHERE arp_id = ".$arp_id;

if ($result = $lc_config->fn_ejecutarquery($lc_sql)) {
    $lc_regs['Confirmar'] = 1;
} else {
    $lc_regs['Confirmar'] = 0;
}

print json_encode($lc_regs);