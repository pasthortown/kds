<?php
session_start();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Toma de Pedido</title>

        <!-- Librerias CSS -->
        <link rel="stylesheet" type="text/css" href="../css/jquery-ui.css"/>
        <link rel="stylesheet" type="text/css" href="../css/alertify.core.css"/>
        <link rel="stylesheet" type="text/css" href="../css/alertify.default.css"/>
        <link rel="stylesheet" type="text/css" href="../css/movimientos.css"/>
    </head>
    <body>
        <div class="contenedor">
            <div class="cntMovimientos" style="width: 800px; height: 620px ">
                <div class="cntTituloMovimiento" >
                    <h3>Registrar Egresos / Ingresos</h3>
                </div>

                <div class="cntTipoMovimiento">
                    <input inputmode="none"  id="tipoEgreso" type="radio" value="-" class="css-checkbox" checked/>
                    <label class="css-label" for="tipoEgreso">Egresos</label>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <input inputmode="none"  id="tipoIngreso" type="radio" value="+" class="css-checkbox" disabled="disabled"/>
                    <label class="css-label" for="tipoIngreso">Ingresos</label>
                </div>

                <div class="cntSelectMovimiento">

                    <label>Movimiento: </label>
                    <select id="tipoMovimiento" ></select>
                </div>
                <div id="divIzquierdo" style="width: 400px; float: left" >    
                    <div class="cntFiltros">
                        <label for="finalFecha" class="labelPadding">Hasta: </label>
                        <input inputmode="none"  type="text" id="finalFecha" value="" disabled/>                                    
                    </div>

                    <div class="numeroAutorizacionn" id="div_numCheque">    
                        <label for="txt_numCheque">Numero: </label>
                        <input inputmode="none"  type="text" id="txt_numCheque" onclick="fn_seteaIdTxt(this.id, 'INT');" value=""/>   
                    </div>

                    <div class="cntEntradas">
                        <label for="totalMovimiento">Total: </label>
                        <input inputmode="none"  type="text" id="totalMovimiento" onclick="fn_seteaIdTxt(this.id, 'FLOAT');" value=""/>                    
                    </div>


                    <div class="cntMensaje">
                        <label>Mensaje: </label>
                        <span id="msnInformacion"></span>
                    </div>
                </div>
                <div id="divDerecho" style="width: 350px; float: right" >    
                    <div class="cntTeclas">
                        <input inputmode="none"  type="button" value="1" class="ui-widget-content ui-corner-all btnNumeros tamanioTeclasCalculadora" onclick="agregarCantidad(1)"/>
                        <input inputmode="none"  type="button" value="2" class="ui-widget-content ui-corner-all btnNumeros tamanioTeclasCalculadora" onclick="agregarCantidad(2)"/>
                        <input inputmode="none"  type="button" value="3" class="ui-widget-content ui-corner-all btnNumeros tamanioTeclasCalculadora" onclick="agregarCantidad(3)"/>
                        <br/>
                        <input inputmode="none"  type="button" value="4" class="ui-widget-content ui-corner-all btnNumeros tamanioTeclasCalculadora" onclick="agregarCantidad(4)"/>
                        <input inputmode="none"  type="button" value="5" class="ui-widget-content ui-corner-all btnNumeros tamanioTeclasCalculadora" onclick="agregarCantidad(5)"/>
                        <input inputmode="none"  type="button" value="6" class="ui-widget-content ui-corner-all btnNumeros tamanioTeclasCalculadora" onclick="agregarCantidad(6)"/>
                        <br/>
                        <input inputmode="none"  type="button" value="7" class="ui-widget-content ui-corner-all btnNumeros tamanioTeclasCalculadora" onclick="agregarCantidad(7)"/>
                        <input inputmode="none"  type="button" value="8" class="ui-widget-content ui-corner-all btnNumeros tamanioTeclasCalculadora" onclick="agregarCantidad(8)"/>
                        <input inputmode="none"  type="button" value="9" class="ui-widget-content ui-corner-all btnNumeros tamanioTeclasCalculadora" onclick="agregarCantidad(9)"/>
                        <br/>
                        <input inputmode="none"  type="button" id="btn_comaCalculadora" value="." class="ui-widget-content ui-corner-all btnNumeros tamanioTeclasCalculadora" onclick="agregarComa()"/>
                        <input inputmode="none"  type="button" value="0" class="ui-widget-content ui-corner-all btnNumeros tamanioTeclasCalculadora" onclick="agregarCero()"/>
                        <input inputmode="none"  type="button" value="<-" class="ui-widget-content ui-corner-all btnNumeros tamanioTeclasCalculadora" onclick="quitarCantidad()"/>
                    </div>
                </div>

                <div class="cntBotones" style="float:left;margin-left:130px;">
                    <input inputmode="none"  type="button" id="btnEnviar" class="boton" value="Crear Movimiento"/>
                    <input inputmode="none"  type="button" id="btnConfirmar" class="botonBloqueado" value="Confirmar Total" disabled="disabled"/>
                    <input inputmode="none"  type="button" id="btnCancelar" class="boton" value="Funciones Gerente"/>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div id="mdl_rdn_pdd_crgnd" class="modal_cargando">
            <div id="mdl_pcn_rdn_pdd_crgnd" class="modal_cargando_contenedor">
                <img src="../imagenes/loading.gif"/>
            </div>
        </div>

        <input inputmode="none"  type="hidden" id="simboloMoneda" value="<?php echo trim($_SESSION['simboloMoneda']); ?>"/>

        <!-- Librerias JavaScript -->
        <script src="../js/jquery.min.js"></script>
        <script type="text/javascript" src="../js/jquery-ui.js"></script>
        <script type="text/javascript" src="../js/idioma.js"></script>
        <script type="text/javascript" src="../js/alertify.js"></script>
        <script type="text/javascript" src="../js/ajax_movimientos.js"></script>

    </body>
</html>