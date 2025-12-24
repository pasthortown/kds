<?php

//////////////////////////////////////////////////////////////
////////DESARROLLADO POR: Worman Andrade//////////////////////
////////DESCRIPCION: Carga de Combos//////////////////////////
/////////////////////En las Mesas/////////////////////////////
///////TABLAS INVOLUCRADAS: ////////////////////////////////// 
///////FECHA CREACION: 16-Enero-2014//////////////////////////
//////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////// 

//verificaci�n de sesi�n iniciada
session_start();
if(!isset($_SESSION['validado'])){										// en caso de no existir sesi�n iniciada, se destruye toda informaci�n
	include_once('../../seguridades/seguridad_niv2.inc');
	}
else {

	include_once('../../system/conexion/clase_sql.php');
	include_once('../../clases/clase_seguridades.php');
	include_once('../../clases/clase_reservas.php');
	
	$mesa 	= new seguridades();
	
	$resId 			= $_SESSION['rstId'];
	$nomRestaurante	= $_SESSION['rstNombre'];
	$codRestaurante	= $_SESSION['rstCodigoTienda'];
	$cadId 			= $_SESSION['cadenaId'];
	$cadena 		= $_SESSION['cadenaNombre'];
	$logotipo 		= $_SESSION['logo'];

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="content-type" content="text/html; charset=ISO-8859-1" /> 
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /> 
    	
    <script type="text/javascript" src="../../js/jquery-1.9.1.js"></script>
    <script type="text/javascript" src="../../js/jquery-ui.js"></script>
    <script type="text/javascript" src="../../js/ajax.js"></script>
    <script type="text/javascript" src="../../js/ajax_reservas.js"></script>
    <script type="text/javascript" src="../../js/timepicker.js"></script>
    <script type="text/javascript" src="../../js/idioma.js"></script>

    
    <link rel="stylesheet" type="text/css" href="../../css/est_pantallas.css"/>
	<link rel="stylesheet" type="text/css" href="../../css/timepicker.css"/>
    <link rel="stylesheet" href="../../css/jquery-ui.css" type="text/css"/>
	<title>Reservas</title>
</head>
<body>
<div class="titulo" align="center">RESERVACIONES</div></div>
<div class"separador"></div>
<div style="width:100%; height: 580px" align="center">
	<div style="float: left; height: auto; width: 25% ">
    <form id="frmReserva" name="frmReserva" action="guardarReserva.php" enctype="multipart/form-data" method="post" />
    <fieldset>
    	<table align="left" border="0" width="100%">
            <tr>
                <td rowspan="2"><img src="../../imagenes/cadena/<?php echo $logotipo; ?>" width="80" height="50" /></td>
                <td align="left"><label><?php echo htmlentities($cadena); ?></label></td>
            </tr>
            <tr>
                <td align="left"><label><?php echo $codRestaurante.'  -  '.htmlentities($nomRestaurante); ?></label></td>
          </tr>
          <tr>
                <td>&nbsp;&nbsp;</td>
                <td>&nbsp;&nbsp;</td>
       	  </tr>
            <tr>
                <td class="titulo_campo"><label>Piso :</label></td>
                <td><select class="combo"  name="piso" id="piso" ></select></td>
            </tr>
            <tr>
                <td class="titulo_campo"><label>&Aacute;rea :</label></td>
                <td><select class="combo"  name="area" id="area" onchange="val()" ></select></td>
            </tr>
   		  <tr>
                <td>&nbsp;&nbsp;</td>
                <td>&nbsp;&nbsp;</td>
       	  </tr>
          <tr>
                <td class="titulo_campo"><label>Motivo:</label></td>
                <td><input inputmode="none"  name="txtMotivo" type="text" id="txtMotivo" /></td>
            </tr>
            <tr>
                <td class="titulo_campo"><label>Fecha:</label></td>
                <td><input inputmode="none"  name="txtfechaI" type="text" id="txtfechaI" size="12"  readonly="readonly" /></td>
            </tr>
            <tr>
                <td class="titulo_campo"><label>Desde:</label></td>
                <td><input inputmode="none"  name="txtHoraI" type="text" id="txtHoraI" size="12"  readonly="readonly"/></td>
                
            </tr>
            <tr>
                <td class="titulo_campo"><label>Hasta</label>:</td>
                <td><input inputmode="none"  name="txtHoraF" type="text" id="txtHoraF" size="12"  readonly="readonly"/></td>
            </tr>
            <tr>
                <td class="titulo_campo"><label>Cliente:</label>:</td>
                <td><input inputmode="none"  type="text" id="txtClienteB" name="txtClienteB" onkeydown="return soloLetras(event)" onkeyup="aMays(event, this)" onclick="fn_consultarCliente()" /></td>
            </tr>
            <tr>
                <td class="titulo_campo"><label>Tel&eacute;fono:</label>:</td>
                <td><input inputmode="none"  type="text" id="txtClienteFono" name="txtClienteFono" onkeydown="return validarNumeros(event)" /></td>
            </tr>
            <tr>
                <td class="titulo_campo"><label>Mesa:</label>:</td>
                <td><input inputmode="none"  type="text" name="txtMesa" id="txtMesa" readonly="readonly">
                	<input inputmode="none"  type="hidden" name="txtMesaId" id="txtMesaId" readonly="readonly"></td>
            </tr>
            <tr>
                <td>&nbsp;&nbsp;</td>
                <td><input inputmode="none"  type="button" value="Reservar" id="Guardar" name="Guardar" onclick="validarCampos()" />&nbsp;&nbsp;
                	<input inputmode="none"  type="reset" value="Cancelar" id="cancelar" name="cancelar" />&nbsp;&nbsp;
                	<input inputmode="none"  type="button" value="Regresar" id="Regresar" name="Regresar" onclick="document.location.href='../userMesas.php'"/></td>
       	  </tr>
        <tr>
			<td width="40" align="center"><img src="../../imagenes/mesa/guia_Cuenta.png" width="40" height="30" alt="Cuenta" /></td><td class="texto_blanco" >Cuenta</td>
        </tr>
        <tr>
			<td width="40" align="center"><img src="../../imagenes/mesa/guia_Reservado.png" width="40" height="30" alt="Reservado" /></td><td class="texto_blanco">Reservado</td>
        </tr>
        <tr>
            <td width="40" align="center"><img src="../../imagenes/mesa/guia_Disponible.png" width="40" height="30" alt="Disponible" /></td><td class="texto_blanco">Disponible</td>
        </tr>
        <tr>
            <td width="40" align="center"><img src="../../imagenes/mesa/guia_En Uso.png" width="40" height="30" alt="En Uso" /></td><td class="texto_blanco">En Uso</td>
        </tr>
        <tr>
            <td width="40" align="center"><img src="../../imagenes/mesa/guia_Mis Mesas.png" width="40" height="30" alt="Mis Mesas" /></td><td class="texto_blanco">Mis Mesas</td>
        </tr>
    </table>
    </fieldset>
    </div>
    	
	<div id="imagen" style="float: right;"></div>
</div>	
  </div>
	<input inputmode="none"  type="hidden" value="<?php echo htmlentities($cadId); ?>" id="txtCadena" />
	<input inputmode="none"  type="hidden" value="<?php echo htmlentities($resId); ?>" id="txtRest" />
 </form>
  
</body>
</html>
<?php } ?>
