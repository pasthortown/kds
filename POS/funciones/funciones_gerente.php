<?php
session_start();

///////////////////////////////////////////////////////////////////////////
////////DESARROLLADO POR JOSE FERNANDEZ////////////////////////////////////
///////////DESCRIPCION: PANTALLA DE FUNCIONES DEL GERENTE//////////////////
////////////////TABLAS: PANTALLA,PERMISOS_PERFIL///////////////////////////
////////FECHA CREACION: 25/03/2014/////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////
///////MODIFICADO POR: Christian Pinto ////////////////////////////////////
///////DESCRIPCION: Cierre Periodo abierto mas de un día //////////////////
///////FECHA MODIFICACIÓN: 07/07/2016 /////////////////////////////////////
///////////////////////////////////////////////////////////////////////////  
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Funciones del Gerente</title>

    <link rel="stylesheet" type="text/css" href="../css/est_pantallas.css"/>
    <link rel="stylesheet" type="text/css" href="../css/alertify.core.css"/>
    <link rel="stylesheet" type="text/css" href="../css/alertify.default.css"/>
    <link rel="stylesheet" type="text/css" href="../css/funcionesGerente.css"/>
</head>
<body>

<input inputmode="none" id="rest_id" type="hidden" value="<?php echo $_SESSION['rstId']; ?>"/>
<!-- interface ger (paramentro)-->
<input inputmode="none" id="IDPeriodo" type="hidden" value="<?php echo $_SESSION['IDPeriodo']; ?>"/>

<input inputmode="none" id="banderaCierrePeriodo" type="hidden" value="<?php echo $_SESSION['sesionbandera']; ?>"/>

<div class="contenedor">
    <div class="cntFuncionesGerente">

        <div class="cntTituloFuncionesGerente">
            <h3>Funciones Gerente</h3>
        </div>

        <div id="cntBotones" class="cntBotones">

        </div>

        <div id="cntFooter" class="cntFooter">
            <div class="cntIzquierda">
                <input inputmode="none"
                       type="button"
                       class="btnApagarEstacion"
                       onclick="fn_apagarEstacion()"
                       value="Apagar"/>
            </div>
            <div class="cntIzquierda">
                <input inputmode="none"
                       type="button"
                       class="btnReiniciarImpresion"
                       onclick="fn_reiniciarImpresion();"
                       value="Reiniciar Impresi&oacute;n"/>
            </div>
            <div class="cntDerecha">
                <input inputmode="none" type="button" class="btnSalir boton" onclick="fn_obtenerMesa()" value="Salir"/>
            </div>
            <div class="cntDerecha">
                <input inputmode="none"
                       type="button"
                       class="btnReiniciarTurnero"
                       onclick="reiniciarTurnero();"
                       value="Turnero"/>
            </div>
        </div>

    </div>
</div>

<!-- interface ger (modal)-->
<div id="mdl_rdn_pdd_crgnd" class="modal_cargando">
    <div id="mdl_pcn_rdn_pdd_crgnd" class="modal_cargando_contenedor">
        <img src="../imagenes/loading.gif"/>
    </div>
</div>

<div id="cntFormulario"></div>

<script type="text/javascript" type="text/javascript" src="../js/jquery-1.9.1.js"></script>
<script type="text/javascript" type="text/javascript" src="../js/jquery-ui.js"></script>
<script language="javascript1.1" type="text/javascript" src="../js/alertify.js"></script>
<script language="javascript" type="text/javascript" src="../js/ajax_funcionesGerente.js"></script>
<script language="javascript" type="text/javascript" src="../js/ajax_cliente_interface.js"></script>
</body>
</html>
