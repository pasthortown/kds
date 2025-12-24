<?php

/*
DESARROLLADO POR: Darwin Mora
DESCRIPCION: Llamada al servicio Web
FECHA CREACION:19/04/2016
FECHA ULTIMA MODIFICACION: 
USUARIO QUE MODIFICO: 
DECRIPCION ULTIMO CAMBIO: 
*/
?>

<script type="text/javascript" src="../../js/jquery183.js"></script>
<script type="text/javascript" src="../../js/jquery.min.js"></script>
<script type="text/javascript" src="../../js/jquery-ui.js"></script>
<script type="text/javascript" src="../../js/ajax.js"></script>
<script type="text/javascript" src="../../js/alertify.js"></script>
<script type="text/javascript" src="../../js/ajax_cliente_interface.js"></script>

<link rel="stylesheet" type="text/css" href="../../css/alertify.core.css" />
<link rel="stylesheet" type="text/css" href="../../css/alertify.default.css" />
<link rel="StyleSheet" href="../../css/interfaceger.css.css" type="text/css"/>

<input inputmode="none"  type="button" onclick="fn_generar_interface()" value=" Generar Interface">

<div id="mdl_rdn_pdd_crgnd" class="modal_cargando">
    <div id="mdl_pcn_rdn_pdd_crgnd" class="modal_cargando_contenedor">
        <img src="../../imagenes/loading.gif"/>
    </div>
</div>
<input inputmode="none"  type="text" id="IDPeriodo" value="566F5FB8-F50F-E611-A9A7-00155D640B01">