<?php
session_start();
//<?php echo $_SESSION['rstId'];
//var_dump($_SESSION);


$hostFormat = strpos($_SESSION['lc_host'], ',');

if($hostFormat){
    $url=explode(",",$_SESSION['lc_host']);
    $ip_servidor=$url[0];
}else{
    $url=explode("\\",$_SESSION['lc_host']);
    $ip_servidor=$url[0];
}


?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Reportes</title>
    <link href="./../../../reportes/ReportesMxp/css/main.css" rel="stylesheet">
</head>
<body>
<input type="hidden" id="restaurante" value="<?php echo $_SESSION['rstId'];?>">
<input type="hidden" id="usuario" value="<?php echo $_SESSION['usuarioId'];?>">
<input type="hidden" id="url_servidor" value="<?php echo 'http://'.$ip_servidor.':3000';?>">
<input type="hidden" id="ubicacion_archivo" value="./../../react_components/reportes/">
<input type="hidden" id="moneda" value="$">
<input type="hidden" id="url_servidor" value="<?php echo 'http://'.$ip_servidor.':3000';?>">
<input type="hidden" id="cadena" value="<?php echo $_SESSION['cadenaId'] ?>">
<div id="app"></div>
<script type="text/javascript" src="./../../../reportes/ReportesMxp/js/main.bundle.js"></script>
</body>
</html>