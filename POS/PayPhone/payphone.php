    <?php
session_start();

if (isset($_POST["factura"]) && isset($_POST["odp_id"]) && isset($_POST["mesa_id"]) && isset($_POST["dop_cuenta"])) {
    ?>

    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml">
        <head>
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

            <title>Toma de Pedido</title>

            <!-- Librerias CSS -->
            <link rel="stylesheet" href="../css/alertify.core.css"/>
            <link rel="stylesheet" href="../css/alertify.default.css"/>
            <link rel="stylesheet" type="text/css" href="../css/teclado.css"/>
            <link rel="StyleSheet" href="../css/payphone.css" type="text/css"/>
        </head>
        <body>
        <!-- Modal structure -->
<div id="modal"> <!-- data-iziModal-fullscreen="true"  data-iziModal-title="Welcome"  data-iziModal-subtitle="Subtitle"  data-iziModal-icon="icon-home" -->
   asdasdasd
</div>
<a href="#" data-izimodal-open="#modal" data-izimodal-transitionin="fadeInDown">Modal</a>

            <div id="contenedor" class="contenedor">
                <div class="cbcr_cntndr">
                    PayPhone
                </div>
                <div class="cnt_rsmn_ttls">
                    <div class="cnt_dtll_rsmn_ttls">
                        <br/>
                        <br/>
                        <h3>Factura</h3>
                        <p id="factura_trnsccn"><?php echo htmlspecialchars($_POST["factura"]); ?></p>
                        <br/>
                        <h3>SubTotal</h3>
                        <p id="sbttl_trnsccn"></p>
                        <br/>
                        <h3>Total</h3>
                        <p id="ttl_trnsccn"></p>
                    </div>
                </div>
                <!-- FORMULARIO -->
                <div class="cnt_frmlr_payphone">
                    <br/>
                    <br/>
                    <h4>Pais</h4>
                    <select id="rgnPayphone"></select>
                    <br/>
                    <br/>
                    <input inputmode="none"  id="celular" type="radio" value="0" name="tipoNickName" class="css-checkbox" checked="checked"/>
                    <label class="css-label radGroup2" for="celular">Celular</label>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <input inputmode="none"  id="cedula" type="radio" value="1" class="css-checkbox" name="tipoNickName"/>
                    <label class="css-label radGroup2" for="cedula">Identificaci&oacute;n</label>
                    <br/>
                    <input inputmode="none"  type="text" id="parametro" class="parametro"/>
                </div>
            </div>

            <div id="mdl_cntdr_payphone" class="modal_preguntas_sugeridas">
                <div id="mdl_rsmn_payphone" class="modal_preguntas_opciones">
                    <div class="dts_trnsccn_tqts">
                        Confirmar Pago PayPhone
                    </div>
                    <div class="tqts_trnsccn_payphone">
                        <br/>
                        <br/>
                        Cliente
                        <h3 id="outCliente"></h3>
                        <br/>
                        Fecha de Pago
                        <h3 id="outFecha"></h3>
                        <br/>
                        Total Transaccion
                        <h3 id="outTotal"></h3>
                        <br/>
                        <br/>
                    </div>
                    <hr/>
                    <div class="btns_payphone">
                        <input inputmode="none"  type="button" id="btn_cancelar" class="btn btn_cancelar" value="Cancelar"/>
                        <input inputmode="none"  type="button" id="btn_confirmar" class="btn btn_confirmar" value="Confirmar"/>
                    </div>
                </div>
            </div>

            <div id="cntFormulario"></div>

            <div id="numPad"></div>
            <div id="txtPad"></div>
            <div id="keyboard"></div>

            <input inputmode="none"  type="hidden" id="factura" value="<?php echo $_POST["factura"]; ?>"/>
            <input inputmode="none"  type="hidden" id="odp_id" value="<?php echo $_POST["odp_id"]; ?>"/>
            <input inputmode="none"  type="hidden" id="mesa_id" value="<?php echo $_POST["mesa_id"]; ?>"/>
            <input inputmode="none"  type="hidden" id="dop_cuenta" value="<?php echo $_POST["dop_cuenta"]; ?>"/>
            <input inputmode="none"  type="hidden" id="simbolo" value="<?php echo $_SESSION['simboloMoneda']; ?>"/>

            <div id="mdl_rdn_pdd_crgnd" class="modal_cargando">
                <div id="mdl_pcn_rdn_pdd_crgnd" class="modal_cargando_contenedor">
                    <img src="../imagenes/loading.gif"/>
                </div>
            </div>

            <!-- Librerias JS -->
            <script type="text/javascript" src="../js/jquery.min.js"></script>
            <script type="text/javascript" src="../js/alertify.js"></script>
            <script type="text/javascript" src="../js/teclado.js"></script>
            <script type="text/javascript" src="../js/payphone.js"></script>
 
        </body>
    </html>

<?php } ?>