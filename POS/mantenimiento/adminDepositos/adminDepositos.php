<?php
session_start();
///////////////////////////////////////////////////////////////////////////
////////DESARROLLADO POR: JOSE FERNANDEZ///////////////////////////////////
///////////DESCRIPCION: PANTALLA DE DEPOSITOS /////////////////////////////
////////////////TABLAS: BILLTE_ESTACION, ARQUEO_CAJA///////////////////////
////////FECHA CREACION: 18-03-2016/////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////
	
	
	include_once"../../system/conexion/clase_sql.php";
	include_once"../../clases/clase_seguridades.php";
	include_once"../../clases/clase_menu.php";
	include_once"../../seguridades/seguridad.inc";

	$lc_moneda='$';//$_SESSION['simboloMoneda'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="content-type" content="text/html; charset=ISO-8859-1" />
    <title>Dep&oacute;sitos</title>
    <!----ESTILOS---> 
    <link rel="stylesheet" href="../../css/jquery-ui.css" type="text/css"/>
    <link rel="stylesheet" type="text/css" href="../../css/est_administracionPantalla.css" />
    <link rel="stylesheet" type="text/css" href="../../css/alertify.core.css"/>
    <link rel="stylesheet" type="text/css" href="../../css/alertify.default.css"/>
    <link rel="stylesheet" type="text/css" href="../../bootstrap/css/bootstrap.css" />
    <link rel="stylesheet" type="text/css" href="../../css/style_usuario.css" />
     <!-- Scripts para scroll-->
    <link rel="stylesheet" href="../../css/jquery.jb.shortscroll.css" />
    <link rel="stylesheet" type="text/css" href="../../css/teclado_cortecaja.css"/>
    <!----JQUERY--->            
    <script src="../../js/jquery1.11.1.js"></script> 
    <!-- Librerias JavaScript -->
    <script src="../../js/jquery-ui.js"></script>  
    <script src="../../js/jquery.numeric.js"></script>  
    <!-- Scripts para scroll-->
    <script type="text/javascript" src="../../js/mousewheel.js"></script>
    <script type="text/javascript" src="../../js/jquery.jb.shortscroll.js"></script>
    <script language="javascript1.1" type="text/javascript" src="../../js/ajax_datatables.js"></script>
    <script language="javascript1.1" type="text/javascript" src="../../bootstrap/js/bootstrap.js"></script>  
    <script language="javascript1.1" type="text/javascript" src="../../bootstrap/js/bootstrap-dataTables.js"></script>
    <script language="javascript1.1"  src="../../js/alertify.js"></script>
    <script type="text/javascript" src="../../js/ajax_adminDepositos.js"></script>
    <script language="javascript1.1" type="text/javascript" src="../../js/js_validaciones.js"></script>
    <script language="javascript" type="text/javascript" src="../../js/teclado_coretecaja.js"></script>
</head>

<body>
<input inputmode="none"  id="idUser" type="hidden" value="<?php echo $_SESSION['usuarioId']; ?>"/>
<div class="superior">
  <div class="menu" style="width: 500px;" align="center">
    <ul>
      <li>
        <input inputmode="none"  id="btn_agregar" type="button"  onclick="fn_accionar('Nuevo')" class="botonhabilitado" value="Agregar"/>
      </li>
    </ul>
  </div>
  <div class="tituloPantalla">
    <h1>DEP&Oacute;SITOS</h1>
  </div>
</div>
</br>
    <div class="contenedor">
        <div class="inferior">
            <div class="panel panel-default">
                <div class="panel-body">
                    <center>
                        <div id="div_tabla_usuarios" class="panel-title"  >
                            <table class="table table-bordered " id="tabla_usuarios"></table>
                        </div>
                        <div class="row" style="height: 350px; width:1160px; ">
<div id="div_fechas" class="col-xs-2" style="height: 360px; overflow-y: auto;">
  <table id="tabla_fechas"></table>
  </div>
  <div align="left" style="width:960px; height:360px; overflow-y: auto;" >
      <table id = 'tabla_estado_usuarios' ></table>
</div>
                        </div>
                    </center>
    </div>
                    <!-- Pie de Página -->
                    <div class="panel-footer"></div>
                </div>
        <!-- Fin Contenedor Inferior -->
        </div>
    <!-- Fin Contenedor -->
    </div>
<!--Inicio div Inactivo-->

<!-------------------------------------INICIO MODAL NUEVO BOOTSTRAP---------------------------------------------->
<div class="modal fade" id="ModalFormasPagoInactivo" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
  <div class="modal-dialog modal-lg ">
    <div class="modal-content">
    <div class="modal-header panel-footer">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="titulomodalNuevo"> </h4>
    </div>
    <br>
      
    <div class="modal-body">       
        <div id=div_formaPago style="height:250px; width:845px; ">
            <table class="table table-bordered " id="formaPago" border="1" cellpadding="1" cellspacing="0">
            </table>
        </div>
    </div>
      
    <div class="modal-footer panel-footer">
        <button type="button" class="btn btn-primary" data-dismiss="modal">Aceptar</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
    </div>
    </div>
   </div>
</div>

<!-------------------------------------FIN MODAL MODIFICAR BOOTSTRAP---------------------------------------------->

<!--Fint div Inactivo-->

<!-------------------------------------INICIO MODAL NUEVO BOOTSTRAP---------------------------------------------->
<div class="modal fade" id="ModalFormasPagoActivo" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
  <div class="modal-dialog modal-lg ">
    <div class="modal-content">
      <div class="modal-header panel-footer">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="titulomodalNuevoActivo"> </h4>
      </div>
      <br>
      
      <div class="modal-body">       
        
             <div id="div_formaPagoActivo" style="height:250px; width:845px; ">
                 <table class="table table-bordered " id="formaPagoActivo" border="1" cellpadding="1" cellspacing="0">
                 </table>
             </div>
             
      </div>
      
      <div class="modal-footer panel-footer" >
            <div align="center">
            	<div class="row" id="pie_formaPagoActivo">	
                	
    			</div>
  			</div>
		</div>
    </div>
   </div>
</div>

<!-------------------------------------FIN MODAL MODIFICAR BOOTSTRAP---------------------------------------------->


<!--  Modal de ingreso de depositos -->
<div class="modal fade" id="modal_ingresoNuevoDeposito" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
  <div class="modal-dialog modal-lg" style="width:965px">
    <div class="modal-content">
      <div class="modal-header panel-footer">
      	
        <h4 class="modal-title" id="titulomodalingresoNuevoDeposito"></h4>
      </div> 
      <div class="modal-body">       
        
        <div id=div_ingresoNuevoDeposito_Cabecera style="height:auto; width:924px;  overflow-y: auto;">
             <table class="table table-bordered table-hover" id="tabla_ingresoNuevoDeposito_Cabecera" border="1" cellpadding="1" cellspacing="0">
             	<tr>
                	<td>N&uacute;mero de referencia:</td><td><input inputmode="none"  id="txt_numReferencia" class="form-control cabecera"></td>
                    <td>Fecha de entrada:</td><td><input inputmode="none"  class="form-control" id="txt_fechaDeEntrada" value="" readonly="readonly"></td>
                </tr>
                <tr>                	
                	<td>Dep&oacute;sito via:</td><td><select id="txt_depositoVia" class="form-control"></select></td>
                    <td>Registrado por:</td><td><input inputmode="none"  class="form-control" readonly="readonly" id="txt_registradoPor" value="<?php echo $_SESSION['usuario']; ?>"></td>
                </tr>
                <tr>
                	<td>C&oacute;digo de referencia:</td><td><input inputmode="none"  class="form-control cabecera" id="txt_codigoReferencia"></td> 
                    <td>Monedas:</td><td><input inputmode="none"  onkeyup="fn_actualizaValorMonedas();" id="txt_monedas"  class="form-control cabecera"></td>                    
                </tr><tr>
                	<td colspan="">Comentario:</td><td colspan="3"> <textarea style="height:60px; resize:none;" class="form-control cabecera" id="txt_AreaNuevo"></textarea></td>               
                </tr>
             </table>
        </div>
        
         <div id=div_ingresoNuevoDeposito style="height:250px; width:924px;  overflow-y: auto;">
             <table class="table table-bordered table-hover" id="tabla_ingresoNuevoDeposito" border="1" cellpadding="1" cellspacing="0">
             </table>
             </div>
             
              <div id=div_tpieingresoNuevoDeposito style="width:924px; ">
             <table class="table table-bordered table-hover" width="900px" id="tpieingresoNuevoDeposito" border="1" cellpadding="1" cellspacing="0">
                <tr align="right" class="success">
                                 <td class="txt_total" width="210px" style="vertical-align:middle" align="right" ><b>Total Dep&oacute;sito>>></b></td>
                                <td class="total" width="320px" style="vertical-align:middle" align="center"></td>
                                <td class="total" width="120px" style="vertical-align:middle" align="center"><b></b></td>
                                <!--<td class="total" width="185px" style="vertical-align:middle" align="center"></td>
                                <td class="total" width="183px" style="vertical-align:middle" align="center"></td>
                                <td width="170px" class="total" style="vertical-align:middle" align="center"></td>
                                <td width="185px" class="total" style="vertical-align:middle"  align="center"></td>--> 
                        </tr> 
             </table>
             </div>
        </div>     
        
        <div class="modal-footer panel-footer">
            <div align="center">
            	<div class="row">	
                	
                    
                    <div class="col-xs-12">
                  <button type="button" class="btn btn-primary" id="btn_okgeneral" onclick="fn_desmontarCajeroArqueo();">Aceptar</button>
                   <button type="button" class="btn btn-default" id="btn_cancelargeneral" data-dismiss="modal" onclick="fn_btnCancelarFormasPago();">Cancelar</button>
                   </div>
                   
               </div>
            </div>    
         </div>                   
  </div>
</div>
</div>
<!-- Fin Modal de ingreso de depositos --!>

<!--  Modal de ingreso de depositos Modificados-->
<div class="modal fade" id="modal_ingresoModificaDeposito" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
  <div class="modal-dialog modal-lg" style="width:965px">
    <div class="modal-content">
      <div class="modal-header panel-footer">      	
        <h4 class="modal-title" id="titulomodalingresoModificaDeposito"></h4>
      </div> 
      <div class="modal-body">       
        
        <div id=div_ingresoModificaDeposito_Cabecera style="height:auto; width:924px;  overflow-y: auto;">
             <table class="table table-bordered table-hover" id="tabla_ingresoModificaDeposito_Cabecera" border="1" cellpadding="1" cellspacing="0">
             	<tr>
                	<td>N&uacute;mero de referencia:</td><td><input inputmode="none"  id="txt_numReferenciaModifica" class="form-control cabeceraModifica"></td>
                    <td>Fecha de dep&oacute;sito:</td><td><input inputmode="none"  class="form-control" id="txt_fechaDeEntradaModifica" value="" readonly="readonly"></td>
                </tr>
                <tr>
                	<td>Dep&oacute;sito via:</td><td><select id="txt_depositoViaModifica" class="form-control"></select></td>
                    <td>Registrado por:</td><td><input inputmode="none"  class="form-control" readonly="readonly" id="txt_registradoPorModifica"></td>
                </tr>
                <tr>
                	<td>C&oacute;digo de referencia:</td><td><input inputmode="none"  class="form-control cabeceraModifica" id="txt_codigoReferenciaModifica"></td> 
                    <td>Monedas:</td><td><input inputmode="none"  id="txt_monedasModifica" class="form-control cabeceraModifica"></td>                    
                </tr>
                <tr>
                	<td colspan="">Comentario:</td><td colspan="3"> <textarea style="height:60px; resize:none;" onclick="" class="form-control" id="txt_AreaModifica"></textarea></td>
                </tr>
             </table>
        </div>
        
         <div id=div_ingresoModificaDeposito style="height:250px; width:924px;  overflow-y: auto;">
             <table class="table table-bordered table-hover" id="tabla_ingresoModificaDeposito" border="1" cellpadding="1" cellspacing="0">
             </table>
             </div>
             
              <div id=div_tpieingresoModificaDeposito style="width:924px; ">
             <table class="table table-bordered table-hover" width="900px" id="tpieingresoModificaDeposito" border="1" cellpadding="1" cellspacing="0">
                <tr align="right" class="success">
                                <td class="txt_total" width="100px" style="vertical-align:middle" align="right" ><b>Totales>>></b></td>
                                <td class="total" width="210px" style="vertical-align:middle" align="center"></td>
                                 <td class="total" width="110px" style="vertical-align:middle" align="center"><b></b></td>
                        </tr> 
             </table>
             </div>
        </div>     
        
        <div class="modal-footer panel-footer">
            <div align="center">
            	<div class="row">	
                	<div class="col-xs-3">
                      <button type="button" id="btn_agregarAjuste" class="btn btn-success" onclick="fn_validaAdmin();" >Agregar Ajuste</button>
                  	</div>
                    
                    <div class="col-xs-6">
                  <button type="button" class="btn btn-primary" id="btn_okgeneralModifica" onclick="fn_asientaDepositoModificado();">Aceptar</button>
                   <button type="button" class="btn btn-default" id="btn_cancelargeneralModifica" data-dismiss="modal" onclick="fn_btnCancelarDepositoModificado();">Cancelar</button>
                   </div>
                   
               </div>
            </div>    
         </div>                   
  </div>
</div>
</div>
<!-- Fin Modal de ingreso de depositos Modificados --!>


<!-------------------------------------INICIO MODAL POR CERRAR---------------------------------------------->
<div class="modal fade" id="modal_formaPago" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
  <div class="modal-dialog modal-lg" style="width:965px">
    <div class="modal-content">
      <div class="modal-header panel-footer">
      	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="titulomodalformapagoPorCerrar"></h4>
      </div> 
      <div class="modal-body">       
        
         <div id=div_formaPagoPorCerrar style="height:340px; width:924px;  overflow-y: auto;">
             <table class="table table-bordered table-hover" id="tabla_formaPagoPorCerrar" border="1" cellpadding="1" cellspacing="0">
             </table>
             </div>
             
              <div id=div_tpie style="width:924px; ">
             <table class="table table-bordered table-hover" width="900px" id="tpie" border="1" cellpadding="1" cellspacing="0">
                <tr align="right" class="success">
                                <td class="txt_total" width="205px" style="vertical-align:middle" align="center" ><b>Totales>>></b></td>
                                <td class="total" width="170px" style="vertical-align:middle" align="center"></td>
                                <td class="total" width="185px" style="vertical-align:middle" align="center"></td>
                                <td class="total" width="183px" style="vertical-align:middle" align="center"></td>
                                <td width="170px" class="total" style="vertical-align:middle" align="center"></td>
                                <td width="185px" class="total" style="vertical-align:middle"  align="center"></td>
                        </tr> 
             </table>
             </div>
        </div>     
        
        <div class="modal-footer panel-footer">
            <div align="center">
            	<div class="row">	
                	<div class="col-xs-3">
                      <button type="button" id="btn_agregarFormaPago" class="btn btn-success" onclick="fn_validaAdmin();" >Agregar Forma de Pago</button>
                  	</div>
                    
                    <div class="col-xs-6">
                  <button type="button" class="btn btn-primary" id="btn_okgeneral" onclick="fn_desmontarCajeroArqueo();">Aceptar</button>
                   <button type="button" class="btn btn-default" id="btn_cancelargeneral" data-dismiss="modal" onclick="fn_btnCancelarFormasPago();">Cancelar</button>
                   </div>
                   
               </div>
            </div>    
         </div>
         
             <!-- <div class="modal-footer panel-footer">
                  <button type="button" class="btn btn-primary" id="btn_okgeneral" onclick="fn_desmontarCajeroArqueo();">Aceptar</button>
                  <button type="button" class="btn btn-default" id="btn_cancelargeneral" data-dismiss="modal" onclick="fn_btnCancelarFormasPago();">Cancelar</button>
              </div>-->
  </div>
</div>
</div>

<!-------------------------------------FIN MODAL POR CERRAR---------------------------------------------->

<!-------------------------------------MODAL BILLETES---------------------------------------------->

<div class="modal fade" id="ModalBilletesDesmontarCajero" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static" style=" padding-bottom:10px">
  <div class="modal-dialog modal-lg" style="width:965px">
    <div class="modal-content">
      <div class="modal-header btn-primary">
        <h4 class="modal-title" id="titulomodalNuevo">Dep&oacute;sitos - Billetes</h4>
      </div> 
      <div class="modal-body">       
        
              <!--<div class="row">-->
              
             <div id=div_billetes style="height:300px; width:920px; overflow-y:auto; ">
  <table class="table table-bordered table-hover" id="billetes" border="1" cellpadding="1" cellspacing="0">
  </table>
</div> 
  <table id="tpie3" border="1" class="table table-bordered table-hover" cellpadding="1" cellspacing="0" style="width:920px;">                                           
                <tr align="right" class="success">
                    <td style="width:440px; vertical-align:middle" align="center"><b>Total Billetes>>></b></td>
                    <td align="center" style="width:220px; text-align:center"></td>
                </tr> 
                
               
                                                                            
  </table>   
          </div>      
         
           <div class="modal-footer panel-footer">
                  <button type="button"  class="btn btn-primary" id="ok" onclick="fn_guardaTotalesBilletes();">Aceptar</button>
                  <button type="button" class="btn btn-default" id="" value="Cancelar" data-dismiss="modal" onclick="fn_cerrarBilletes();">Cancelar</button>
           </div>
                          
    </div>
  </div>
</div>

<!-------------------------------------FIN MODAL BILLETES---------------------------------------------->

<!--MODAL TARJETAS-->
<div class="modal fade" id="ModalTarjetas" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
  <div class="modal-dialog" style="width:330px">
    <div class="modal-content">
      <div class="modal-header btn-primary">
        <h4 class="modal-title" id="titulomodalNuevo"></h4>
      </div> 
      <div class="modal-body">       
		<div id="dialogTarjetas" style="width:100%"></div>
    </br>
   
    </div>
  </div>
</div>
</div>
<!--FIN MODAL TARJETAS-->

<div class="modal fade" id="ModalMotivo" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
  <div class="modal-dialog" style="">
    <div class="modal-content">
      <div class="modal-header btn-primary">
        <h4 class="modal-title" id="titulomodalNuevo">Ingrese el Motivo del descuadre de valores </h4>
      </div> 
      <div class="modal-body">       
        
             <table align="center">
                <tr>
                    <td>
                        <textarea style="width:560px; height:80px; text-transform:uppercase; resize:none; font-size: 25px" onclick="" class="form-control" id="txtArea"></textarea>
                    </td>
                </tr>
            </table>               
            </br>
           
            <table style="position:inherit; z-index: 3;">
                <tr>
                    <td>
                        <div id="motivos_descuadre1" style="position:inherit; z-index: 3;"></div>
                    </td>
                </tr>
            </table>
			</div>
            
              <div class="modal-footer panel-footer">
                  <button type="button"  class="btn btn-primary " id="btn_okmotivo" onclick="">Aceptar</button>
                  <button type="button" class="btn btn-default" id="btn_cmotivo" value="Cancelar" data-dismiss="modal" onclick="">Cancelar</button>
             </div>   
  </div>
</div>
</div>

<div class="modal fade" id="modal_agregarAjuste" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
   <div class="modal-dialog" style="width:400px" >
    <div class="modal-content">
      <div class="modal-header btn-primary">
        <h4 class="modal-title" id="titulomodalNuevo">Agregar Ajuste</h4>
      </div> 
      <div class="modal-body"> 
            
	  	 <div class="row">
         	<!--<div class="col-xs-1"></div>-->
          	<!--<div class="col-xs-3">
            	<h5>Seleccione:</h5>
          	</div>-->
            
          	<div class="col-xs-10 col-md-offset-1">
            	<div class="form-group" class="col-xs-1">
                	<div align="center" data-toggle="buttons">                         
                        <label id="opt_Mas" class="btn btn-default bt btn-lg">
                            <input inputmode="none"  type="radio" name="options" id="rd_mas" autocomplete="off" value="+">+
                        </label>                        
                        <label id="opt_Menos" class="btn btn-default btn-lg">
	                        <input inputmode="none"  type="radio" name="options" id="rd_menos" autocomplete="off" value="-">-
    	                </label>
                    </div>
                    <br/>
                	<table class="table table-bordered table-hover" id="tabla_ingresoNuevoDeposito" border="1" cellpadding="1" cellspacing="0">                    	<tr>
                        	<td align="center"><label style="font-size:30px"><?php echo $lc_moneda; ?></label><input inputmode="none"  class="btn-lg" type="text" id="txtAjuste"/></td>
                        </tr>
             		</table>
                	<br/>
                    
                    <div align="center" data-toggle="buttons">                         
						<select id="sel_Ajuste" class="form-control"></select>                           
                    </div>                                                               	 
            	</div>
          	</div>
            
          <div class="col-xs-1"></div>
        </div>
             </br>
             </br>
             </br>
	</div>
        <div align="center" class="modal-footer panel-footer">
          <button type="button" id="btn_ok_FormasPago" class="btn btn-primary " onclick="fn_agregarFormaPago();">Aceptar</button>
           <button type="button" class="btn btn-default" id="btn_cancelar_FormasPago" onclick="fn_cerrarModalAjuste()" data-dismiss="modal" value="Cancelar">Cancelar</button>
        </div>    

   
    
  </div>
</div>
</div>

<!--MODAL TARJETAS-->
<div class="modal fade" id="ModalTecladoNuevaFormaPago" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
  <div class="modal-dialog" style="width:330px">
    <div class="modal-content">
      <div class="modal-header btn-primary">
        <h4 class="modal-title" id="tituloModalTecladoNuevaFormaPago"></h4>
      </div> 
      <div class="modal-body">       
		<div id="tecladoNuevaFormaPago" style="width:100%"></div>
    </br>
   
    </div>
  </div>
</div>
</div>
<!--FIN MODAL TARJETAS-->
<input inputmode="none"  id="hide_fecha"  type="hidden" value=""/>
<input inputmode="none"  id="elimina" type="hidden" values=""/>
<input inputmode="none"  type="hidden" id="hid_controlEfectivo"/>
<input inputmode="none"  type="hidden" id="hid_estacion"/>
<input inputmode="none"  type="hidden" id="hid_usuario"/>
<input inputmode="none"  type="hidden" id="hid_controlMesa"/>
<input inputmode="none"  type="hidden" id="hid_controlCuenta"/>
<input inputmode="none"  type="hidden" id="hid_controlEstacion"/>
<input inputmode="none"  type="hidden" id="hid_usuarioDescripcion"/>
<input inputmode="none"  type="hidden" id="hid_controlDiferencia"/>
<input inputmode="none"  type="hidden" id="hid_descuadre"/>
<input inputmode="none"  type="hidden" id="hid_restaurante"/>
<input inputmode="none"  type="hidden" id="hide_totalBilletesEfectivo"/>
<input inputmode="none"  type="hidden" id="hide_totalPosEfectivo"/>
<input inputmode="none"  type="hidden" id="valorEfectivoTotal"/>
<input inputmode="none"  type="hidden" id="hide_totalBilletes"/>
<input inputmode="none"  type="hidden" id="hid_usuario_efectivo"/>
<input inputmode="none"  type="hidden" id="hid_formaPago"/>
<input inputmode="none"  type="hidden" id="array"/>
<input inputmode="none"  type="hidden" id="hid_diferencia"/>
<input inputmode="none"  type="hidden" id="hid_totalNuevo" />
<input inputmode="none"  type="hidden" id="hid_masomenos">
<input inputmode="none"  type="hidden" id="retiroEfectivoModalBilletes">
<input inputmode="none"  type="hidden" id="totalPos">
<input inputmode="none"  type="hidden" id="valorsumabilletes">
<input inputmode="none"  type="hidden" id="valormasomenos">
<input inputmode="none"  type="hidden" id="totalPosCalculado">
<input inputmode="none"  type="hidden" id="diferenciaTotales">
<input inputmode="none"  type="hidden" id="hid_controlRetiroEfectivo">
<input inputmode="none"  type="hidden" id="totalNuevoEfectivo" >
<input inputmode="none"  type="hidden" id="hid_usr_id_cajero" >
<input inputmode="none"  type="hidden" id="hide_periodo" >
<input inputmode="none"  type="hidden" id="hide_nombre" >
<input inputmode="none"  type="hidden" id="id_formaPago" >
<input inputmode="none"  type="hidden" id="hide_fmp_descripcion" >
<input inputmode="none"  type="hidden" id="hide_fmp_descripcion_val" >
<input inputmode="none"  type="hidden" id="hide_num_cupones" >
<input inputmode="none"  type="hidden" id="hide_fechaPeriodo" >
<input inputmode="none"  type="hidden" id="hide_codigoDeposito">
<input inputmode="none"  type="hidden" id="hide_totalDepositoBilletes">
<input inputmode="none"  type="hidden" id="hide_codigoDepositoModificado">
<input inputmode="none"  type="hidden" id="hide_codigoPeriodoModificado">
<input inputmode="none"  type="hidden" id="hide_totalMonedasNuevo">
<input inputmode="none"  type="hidden" id="hide_totalMonedasModifica">
<input inputmode="none"  type="hidden" id="hide_moneda" value="<?php echo $lc_moneda; ?>">

</body>
</html>