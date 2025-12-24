<?php
//////////////////////////////////////////////////////////////
////////DESARROLLADO POR: WORMAN ANDRADE//////////////////////
////////DESCRIPCION: CLIE///NTES//////////////////////////////
//////////////////////////////////////////////////////////////
///////TABLAS INVOLUCRADAS: ////////////////////// ///////////
//////////////////////////////////////////////////////////////
///////FECHA CREACION: 10-02-2014/////////////////////////////
//////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////// 
/*
if(!isset($_SESSION['validado'])){										// en caso de no existir sesión iniciada, se destruye toda información
	include_once('../seguridades/seguridad.inc');
} else {
	include_once("../system/conexion/clase_sql.php");
	include_once("../clases/clase_seguridades.php");
	include_once("../clases/clase_clientes.php");
	session_start();
	$lc_UsuarioId		= $_SESSION['usuarioId'];
	$lc_perfilUsuario	= $_SESSION['perfil'];
	$lc_rst				= $_SESSION['rstId'];
	$lc_codigoRst		= $_SESSION['rstCodigoTienda'];
	$lc_nombreRst		= $_SESSION['rstNombre'];
	$lc_cdnId			= $_SESSION['cadenaId'];
	$lc_logo			= $_SESSION['logo'];
	$lc_tipoServicio	= $_SESSION['TipoServicio'];*/

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Clientes</title>
	<script src="../js/w_jquery.js"></script>
    <script type="text/javascript" src="../js/jquery.min.js"></script>
	<script type="text/javascript" src="../js/ajax_clientes.js"></script>
    <script type="text/javascript" src="../js/jquery-ui.js"></script>
    <script type="text/javascript" src="../js/ajax.js"></script>
    <script type="text/javascript" src="../js/alertify.js"></script>
    <script type="text/javascript" src="../js/ajax_pad.js"></script>
	<script type="text/javascript" src="../js/ajax_api_masterdataclientes.js"></script>
    	
    <link rel="stylesheet" type="text/css" href="../css/style_clientes.css" />   
   	<link rel="stylesheet" type="text/css" href="../css/style_pad.css"/>
    <link rel="stylesheet" type="text/css" href="../css/est_modal.css" />
	<link rel="stylesheet" type="text/css" href="../css/est_pantallas.css" />
    <link rel="stylesheet" type="text/css" href="../css/alertify.core.css" />
	<link rel="stylesheet" type="text/css" href="../css/alertify.default.css" />

</head>

<body>

<div id="ventanaVisuliza">
<table align="center" width="780px" border="0">
  	<tr>
		<td align="center" width="95px"><img src="../imagenes/cadena/<?php echo $_SESSION['logo']; ?>" width="80px" height="40px" /></td>
		<td align="center" class="titulo">DATOS DEL CLIENTE</td>
      </tr>
</table>
<br/>
<table align="center" width="780px" border="0">
	<tr>
    	<td align="left" width="50%" class="tituloMesa">CI / RUC:<br />
            <input inputmode="none"  type="text" id="txtClienteCI" size="15" maxlength="13" onclick="fn_numerico(this)"/>
            <img id="lupaCliente" src="../imagenes/lupa.png" width="25px" height="15px" alt="Buscar" onclick="fn_clienteBuscar()" />
    	</td>
    </tr>
	<tr>
    	<td align="left" width="50%" class="tituloMesa">Nombre:<br />
        	<input inputmode="none"  size="50" type="text" id="txtClienteNombre" name="txtClienteNombre" />
		</td>
        <td align="left" width="50%" class="tituloMesa">Apellidos:<br />
        	<input inputmode="none"  size="50" type="text" id="txtClienteApellido" name="txtClienteApellido" />
		</td>
	<tr/>
        <td align="left" width="50%" class="tituloMesa">Ciudad:<br/>
        	<input inputmode="none"  type="text" id="txtClienteCiudad" >
            <select id="txtClienteCiudadList" > </select>
        </td>
        <td align="left" width="50%" class="tituloMesa">Tel&eacute;fono<br/>
        	<input inputmode="none"  type="text" id="txtClienteFono" size="15" maxlength="10" /></td>
	</tr>
    <tr>
    	<td align="left" width="113" class="tituloMesa">Direcci&oacute;n:<br/>
  		<input inputmode="none"  type="text" id="txtClienteDireccion" size="50" maxlength="50" /></td>  		
	
    	<td align="left" width="113" class="tituloMesa">Correo Electr&oacute;nico:<br/>
  		<input inputmode="none"  type="text" id="txtCorreo" size="50" maxlength="50" />
	</td></tr>
</table>
<br/>
<table align="center" border="0" width="90%">
	<tr>
    	<td align="center">
        	<img id="btnClienteNuevo" src="../imagenes/botones/btnAgregar.png" width="60px" height="50px" alt="Nuevo" title="Nuevo Cliente" onclick="fn_clienteNuevo()" />
            <img id="btnClienteModificar" src="../imagenes/botones/btnModificar.png" width="60px" height="50px" alt="Editar" title="Editar Cliente" onclick="fn_clienteModificar()"/>
            <img id="btnClienteGuardar" src="../imagenes/botones/btnGuardar.png" width="60px" height="50px" alt="Guardar" title="Guardar Cliente" onclick="fn_clienteGuardar()" />
            <img id="btnClienteGuardarActualiza" src="../imagenes/botones/btnGuardar.png" width="60px" height="50px" alt="Guardar" title="Guardar Cliente" onclick="fn_clienteGuardarActualiza()" />
			<img id="btnClienteCancelar" src="../imagenes/botones/btnCancelar.png" width="60px" height="50px" alt="Cancelar" title="Cancelar" onclick="fn_clienteCancelar()" />
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <img id="btnConsumidorFinal" src="../imagenes/botones/btnConsumidorFinal.png" width="60px" height="50px" alt="Consumidor Final" title="Consumidor Final" onclick="fn_consumidorFinal()">
            <img id="btnFacturaImprimir" src="../imagenes/botones/btnImprimir.png" width="60px" height="50px" alt="Imprimir" title="Imprimir Factura"title="Pagar" onclick="fn_printFactura()">
            <img id="btnSalir" src="../imagenes/botones/btnSalir.png" width="60px" height="50px" alt="Salir" title="Salir" title="Salir" onclick="fn_cerrar()">
      </td>
    </tr>
 </table>
 <div id="numPad"></div>
 <div id="txtPad"></div>
 <div id="keyboard">
 
</div>
</body>
</html>
<?php //} ?>