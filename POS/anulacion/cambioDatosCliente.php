<?php
/*
  -------------------------------------------------------------------------------
  FECHA CREACION      : 24-03-2014
  DESARROLLADO POR    : Cristhian Castro
  DESCRIPCION         :
  -------------------------------------------------------------------------------
  FECHA MODIFICACION  : 07-05-2014
  MODIFICADO POR      : Cristhian Castro
  DECRIPCION CAMBIO   : Documentación y validación en Internet Explorer 9
  -------------------------------------------------------------------------------
  FECHA MODIFICACION  : 22-05-2015
  MODIFICADO POR      : Christian Pinto
  DECRIPCION CAMBIO   : Aumento de campos cajero y estado factura, permisos para reimprimir factura
  -------------------------------------------------------------------------------
  FECHA MODIFICACION  : 02/03/2017
  MODIFICADO POR      : Daniel Llerena
  DECRIPCION CAMBIO   : Nota de crédito -cambio datos de cliente
  -------------------------------------------------------------------------------
  FECHA MODIFICACION  : 10/09/2018
  MODIFICADO POR      : Juan Esteban Canelos
  DECRIPCION CAMBIO   : Reverso de recargas - amigos Juan Valdez
  -------------------------------------------------------------------------------
  FECHA MODIFICACION  : 18/09/2024
  MODIFICADO POR      : Jhonatan Palencia
  DECRIPCION CAMBIO   : Impresión en estación cambio de datos cliente
  -------------------------------------------------------------------------------
 */

include_once "../system/conexion/clase_sql.php";
include_once "../clases/clase_anularOrden.php";
include_once "../seguridades/seguridad_niv2.inc";


$lc_ip = $_SESSION["direccionIp"];
$lc_UsuarioId = $_SESSION['usuarioId'];
$lc_perfilUsuario = $_SESSION['perfil'];
$lc_rst = $_SESSION['rstId'];
$tomaPedido = new menuPedido();

  //validar que el restaurant tiene activa la politica para validar email con plugthem
$resultado_verificar_email_plug = $tomaPedido->fn_consulta_generica_escalar("EXEC dbo.verificarPoliticaValidaEmailPlugthemSimplified '%s'", $lc_rst);
$restaurant_valida_email = isset($resultado_verificar_email_plug["Activo"]) ? $resultado_verificar_email_plug["Activo"] : 0;


?>

<link rel="stylesheet" type="text/css" href="../css/CambioDatosCliente.css"/>
<link rel="stylesheet" type="text/css" href="../css/interfaceger.css"/>

<style>
    .tecladoCredenciales {        
        z-index: 99999;
        height: 500px;
    }

    #alertify-cancel {
        display: initial !important;
    }

    .sri-leyenda {
        margin: 100px 0;
        padding: 20px 10px 20px 10px;
        border: #1c4ec4 2px solid;
        border-radius: 20px;        
    }

    .sri-titulo {
        margin: 10px auto;
        text-align: center;
    }

    .sri-articulo, .sri-nota {
        margin: 10px auto;
        position: relative;
        left: 20px; 
    }

    .sri-info {
        margin: 5px 10px;
        position: relative;
        left: 20px;
    }

    #datosFactura {
        overflow: hidden;
    }
</style>

<!-- Variables ocultas-->
<input inputmode="none"  type="hidden" id="hid_tecladoIdentificacion" />
<input inputmode="none"  type="hidden" id="hid_codigoFactura" />
<input inputmode="none"  type="hidden" id="hid_usuarioAdmin" />
<input inputmode="none"  type="hidden" id="hid_CodigoNuevaCredito" />
<input inputmode="none"  type="hidden" id="hid_IpEstacionCambioDatos" value="<?php echo $lc_ip; ?>" />
<input inputmode="none"  type="hidden" id="clienteAutorizacion"  />
<input inputmode="none"  type="hidden" id="clienteEstado" />
<input inputmode="none"  type="hidden" id="estadoWS" />
<input inputmode="none"  type="hidden" id="confirmarAnulacion" />

<!-- Modal Cambio Datos Cliente -->
<div id="datosFactura">     
    <table align="center" width="900px" border="0">
        <tr>
            <td align="left" class="tituloCabecera" style="padding-bottom:10px;">
                <label id="documento_obligatorios" style="color:#F00" >(*)</label> N&uacute;mero Documento:<br />
                <input inputmode="none"  type="text" id="txtClienteCI" onclick="fn_validaTecladoIdentificacion(this);" onchange="fn_clienteCambioDatosBuscar(<?php echo $restaurant_valida_email; ?>);" style="border-radius:8px; font-size:24px; height:40px;" maxlength="15"/>
            </td>
            <td>
                <input inputmode="none"  type="button" class="btnRucCiActivo" id="rdo_ruc" onclick="fn_validaTecladoCedulaRuc();" value="CI / RUC" style="margin-left:50px"/>
                <input inputmode="none"  type="button" class="btnRucCiInactivo" id="rdo_pasaporte" onclick="fn_validaTecladoPasaporte();" value="PASAPORTE"/>
            </td>
            <td align="right">
                <button id="btnNuevoCliente" class="botonesbarra" onclick="fn_validaCamposCliente('I', <?php echo $restaurant_valida_email; ?>);">Cambiar Datos</button>
                <button id="btnClienteConfirmarDatos" class="botonesbarra" onclick="fn_validaConfirmarCambioDatos();">Confirmar</button>
                <button id="btnClienteAnularFactura" class="botonesbarra" onclick="fn_validaCamposCliente('U', <?php echo $restaurant_valida_email; ?>);">Cambiar Datos</button>
                <button id="btnClienteCancelarAnulacion" class="botonesbarra" onclick="fn_botonCancelar();">Cancelar</button>
            </td>
        </tr>
        <tr>
            <td colspan="2" align="left" width="50%" class="tituloCabecera" style="padding-bottom:10px;">
                <label id="nombres_obligatorios" style="color:#F00" >(*)</label> Nombres:<br />
                <input inputmode="none"  onclick="fn_alfanumericoCliente(this); fn_ocultarTecladoAlfanumerico(numPadCliente);" onchange="fn_focoinput_telefono();" size="30" type="text" id="txtClienteNombre" name="txtClienteNombre" style="border-radius:8px; text-transform:uppercase; width:586px; height:40px;"/>
            </td>              
        </tr>
        <tr>
            <td align="left" class="tituloCabecera" > Tel&eacute;fono<br/>
                <input inputmode="none"  onclick="fn_alfanumericoTelefono(this); fn_ocultarTecladoAlfanumerico(numPadCliente);" onchange="fn_focoinput_email();" type="text" id="txtClienteFono" size="20" maxlength="10" style="border-radius:8px; width:250px; height:40px;"/>
            </td>
            <td align="left" class="tituloCabecera" > Correo Electr&oacute;nico:<br/>
                <input inputmode="none"  onclick="fn_tecladoAlfanumerico(this); fn_ocultarTecladoAlfanumerico(numPadCliente);" onchange="fn_focoinput_nombres();fn_validaEmail(<?php echo $restaurant_valida_email; ?>);" type="text" id="txtCorreo" size="30" maxlength="50" style="border-radius:8px; width:470px; height:40px;"/>
            </td>
        </tr>
        <tr>
            <td>
                <label id="campos_obligatorios" style="color:#F00"> (* Campos Obligatorios) </label>
            </td>
        </tr>
    </table>
    
    <!-- Teclado para digitar numero de documento -->    
    <div id="numPadCliente" style="font-size: 34px;"></div>
    <div id="txtPadCliente"></div>    
    <div id="sri_leyenda" class="sri-leyenda"></div>
    <div id="keyboardCliente" style="font-size: 34px;"></div>
    <div id="dominio1"></div>  
    <div id="dominio2"></div>
    
</div>

<!-- Contenedor utilizado para el CARGANDO -->
<div id="mdl_rdn_pdd_crgnd" class="modal_cargando" style="display: none;">
    <div id="mdl_pcn_rdn_pdd_crgnd" class="modal_cargando_contenedor" style="height: 220px; width: 220px;">
        <img src="../imagenes/loading.gif"/>
    </div>
</div>

<div id="tecladoCredenciales" style="width: 500px; height: 500px;">
    <div class="preguntasTitulo">Ingrese Credenciales de Administrador</div>
    <div class="anulacionesSeparador">
        <div class="anulacionesInput">
            <input inputmode="none"  type="password" name="usr_clave" id="claveAdmin" style="height: 35px; width: 454px; font-size: 16px;"/>
        </div>
    </div>
    <div id="tecladoAdmin" align="center" style="left: 17%; font-size: 34px;"></div>
</div>

<script type="text/javascript" src="../js/jquery.min.js"></script>
<script type="text/javascript" src="../js/tecladoNCCliente.js"></script>
<script type="text/javascript" src="../js/ajax_ValidaDocumento.js"></script>