<?php
/* * *******************************************************
 *          DESARROLLADO POR: Alex Merino                *
 *          DESCRIPCION: PHP vista -(Bines) php        *
 *          FECHA CREACION: 16/04/2018                   *
 * ******************************************************* */

session_start();
include_once("../../seguridades/seguridad.inc");

if (!isset($_SESSION['validado'])) {
    include_once('../../seguridades/Adm_seguridad.inc');
} else {
    include_once('../../system/conexion/clase_sql.php');
    include_once('../../clases/clase_seguridades.php');
    include_once('../../clases/clase_adminConfiguracionBines.php');
    $bines = new bines();
    ?>
    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml">   
        <head>
            <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />                
            <!-- LIBRERIAS -->   
            <!---------------------------------------------------
            ESTILOS
    ----------------------------------------------------->  
            <link rel="stylesheet" href="../../css/jquery-ui.css" type="text/css"/>
            <!--<link rel="stylesheet" href="../../css/est_pantallas.css" type="text/css"/>-->  
            <link rel="stylesheet" type="text/css" href="../../bootstrap/css/bootstrap.css " />            
            <link rel="stylesheet" type="text/css" href="../../bootstrap/templete/css/bootstrap.css " />        
            <link rel="stylesheet" type="text/css" media="all" href="../../bootstrap/css/daterangepicker-bs3.css" />      
            <link rel="stylesheet" type="text/css" href="../../bootstrap/css/switch.css" />       
            <link rel="stylesheet" type="text/css" href="../../css/alertify.core.css"/>        
            <link rel="stylesheet" type="text/css" href="../../css/alertify.default.css"/>    
            <link rel="stylesheet" type="text/css" href="../../css/est_administracionPantalla.css" />  
            <link rel="stylesheet" type="text/css" href="../../bootstrap/css/bootstrap.css" />  
            <!--<link rel="stylesheet" type="text/css" href="../../bootstrap/css/bootstrap-toggle.min.css" />-->
            <link rel="stylesheet" type="text/css" href="../../css/chosen.css" />                 
            <title>Administraci&oacute;n - Bines</title>                 
        </head>
        <body>
            <input inputmode="none"  id="idUser" type="hidden" value="<?php echo $_SESSION['usuarioId']; ?>"/>   
            <input inputmode="none"  id="cadenas" type="hidden" value="<?php echo $_SESSION['cadenaId']; ?>"/>      
            <input inputmode="none"  id="restaurante" type="hidden" value="<?php echo $_SESSION['rstId']; ?>"/>   

            <div class="superior">        
                <div class="menu" style="width: 400px; align: center; margin-top: 5px;">    
                    <ul>  
                        <li>        
                            <input inputmode="none"  id="btn_agregar" type="button" onclick="fn_accionar('Nuevo')" class="botonhabilitado" value="Agregar"/>
                            <!--<input inputmode="none"  style="margin-top: 7px;" id="btn_agregar" type="button" onclick="fn_agregar()" class="botonhabilitado" value="Agregar"/>-->
                        </li>
                        <!-- <li><input inputmode="none"  id="btn_cancelar" type="button" onclick="fn_borrar()" class="botonhabilitado" value="Cancelar"/></li>-->  
                        <!-- <li><input inputmode="none"  id="btn_agregar" type="button" onclick="fn_agregarcate()" class="botonhabilitado" value="Agregar"/></li>  -->   
                    </ul>
                </div> 
                <div class="tituloPantalla">   
                    <h1>BINES</h1>     
                </div>   
            </div> 
            <div class="contenedor">       
                <div class="inferior">         

                    <br />
                    <br />

                    <div class="contenedor" >
                        <div class="inferior" align="center" >
                            <!--<div class="panel panel-default text-left">      
                                <div class="panel-body">      
                                    <tr>
                                        <td width="150">Seleccionar Pais: </td>
                                        <td>
                                            <select id="selpais" class="form-control" ></select>
                                        </td>
                                    </tr>                                      
                                </div> 
                            </div>  -->
                            <div id="tabla_bines">                                           
                                <table class="table table-bordered table-hover" id="detalle_bines" border="1" cellpadding="1" cellspacing="0">                                    
                                </table>  
                            </div>                            
                        </div>                        
                    </div>                    
                    <div id="load">                        
                    </div>  
                    <!-- MODAL PARA NUEVO BINES -->
                    <div class="modal fade" id="ModalNuevo" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">                    
                        <div class="modal-dialog modal-lg">      
                            <div class="modal-content">        
                                <div class="modal-header panel-footer">    
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>                                   
                                    <b>   
                                        <h4 class="modal-title" id="titulomodalNuevo"/>
                                    </b>
                                </div>   
                                <ul id="myTabs" class="nav nav-tabs" role="tablist">  
                                    <li role="presentation" class="active"><a href="#empresa" aria-controls="empresa" role="tab" data-toggle="tab">
                                            <span class="glyphicon glyphicon-tasks" style="font-size: 20px;"></span>
                                            &nbsp;Información Bines
                                        </a></li>
                                </ul>   
                                <br/>  
                                <div align="right" class="col-xs-12 col-x"> <b>Est&aacute; Activo?:                                                                         
                                        <input inputmode="none"  type="checkbox" id="option_new" /> </b>
                                </div>
                                <div class="modal-body">   
                                    <div role="tabpanel">                                          
                                        <div id="pst_cnt" class="tab-content">                                             
                                            <div class="tab-content">
                                                <div role="tabpanel" class="tab-pane active" id="empresa">  
                                                    <div class="row" style="display: none">  
                                                        <div class="col-xs-1"></div>  
                                                        <div class="col-xs-3">
                                                            <h5>Política:</h5>  
                                                        </div>     
                                                        <div class="col-xs-7">
                                                            <div id="politicas"></div>                                                            
                                                        </div>
                                                        <div class="col-xs-1"></div>      
                                                    </div>
                                                    <br/>
                                                    <div class="row">
                                                        <div class="col-xs-1"></div>
                                                        <div class="col-xs-3">
                                                            <h5>Bin inicial:</h5> 
                                                        </div>
                                                        <div class="col-xs-7">
                                                            <div id="divminimo"></div>                                                                
                                                        </div>

                                                        <div class="col-xs-1"></div>   
                                                    </div> 
                                                    <br/>
                                                    <div class="row">
                                                        <div class="col-xs-1"></div>
                                                        <div class="col-xs-3">
                                                            <h5>Bin final:</h5>
                                                        </div>   
                                                        <div class="col-xs-7">
                                                            <div id="divmaximo"></div>
                                                        </div>
                                                        <div class="col-xs-1"></div>      
                                                    </div>                                                    
                                                    <br/>                                                    
                                                    <div class="row">
                                                        <div class="col-xs-1"></div>
                                                        <div class="col-xs-3">
                                                            <h5>Definición de Política:</h5>
                                                        </div>   
                                                        <div class="col-xs-7">
                                                            <div id="definiciones" ></div>
                                                        </div>
                                                        <div class="col-xs-1"></div>      
                                                    </div>  
                                                    <br/>                                                    
                                                    <div class="row">
                                                        <div class="col-xs-1"></div>
                                                        <div class="col-xs-3">      
                                                            <h5>Forma de pago:</h5>
                                                        </div>   
                                                        <div class="col-xs-7">  
                                                            <div id="formaPago"></div>                                                        
                                                        </div>
                                                        <div class="col-xs-1"></div>   
                                                    </div>                                                     
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                                <div class="modal-footer panel-footer" id="botonesguardarcancelar">  
                                    <button type="button" class="btn btn-primary" id='botonGuardarEstacion' onclick="fn_guardarCambios(1);" >Aceptar</button>
                                    <button type="button" class="btn btn-default" onclick="fn_close()" data-dismiss="modal">Cancelar</button>
                                </div>
                                <div class="modal-footer panel-footer" id="botonessalir">   
                                    <button type="button" class="btn btn-primary" id='botonGuardarEstacion' data-dismiss="modal">Aceptar</button>
                                    <button type="button" class="btn btn-default" onclick="" data-dismiss="modal" style="width:10%">Salir</button>
                                </div>                             

                            </div>
                        </div>
                    </div>
                    <!-- END MODAL PARA NUEVO BINES-->
                    <!-- INICIO MODAL PARA MODIFICAR BINES -->
                    <div class="modal fade" id="ModalModificar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">                    
                        <div class="modal-dialog modal-lg">      
                            <div class="modal-content">        
                                <div class="modal-header panel-footer">    
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>                                   
                                    <b>   
                                        <h4 class="modal-title" id="titulomodalModificar"/>
                                    </b>
                                </div>   
                                <ul id="myTabs" class="nav nav-tabs" role="tablist">
                                    <li role="presentation" class="active"><a href="#empresa" aria-controls="empresa" role="tab" data-toggle="tab">
                                            <span class="glyphicon glyphicon-tasks" style="font-size: 20px;"></span>
                                            &nbsp;Información Bines
                                        </a></li>
                                </ul>   
                                <br/>  
                                <div align="right" class="col-xs-12 col-x"> <b>Est&aacute; Activo?:                                                                         
                                        <input inputmode="none"  type="checkbox" id="opcion_Modificar" /> </b>
                                </div>
                                <div class="modal-body">   
                                    <div role="tabpanel">                                        
                                        <div id="pst_cnt" class="tab-content">                                           
                                            <div class="tab-content">
                                                <div role="tabpanel" class="tab-pane active" id="empresa">
                                                    <div class="row" style="display: none">
                                                        <div class="col-xs-1"></div>
                                                        <div class="col-xs-3">
                                                            <h5>Id Cadena:</h5>
                                                        </div>   
                                                        <div class="col-xs-7">
                                                            <div class="form-group" class="col-xs-1">                                           
                                                                <input inputmode="none"  type="text" style="text-align:left" class="form-control" id="txtidcadena" />
                                                            </div>
                                                        </div>
                                                        <div class="col-xs-1"></div>      
                                                    </div>
                                                    <div class="row" style="display: none">
                                                        <div class="col-xs-1"></div>
                                                        <div class="col-xs-3">
                                                            <h5>Id Cadena Coleccion:</h5>
                                                        </div>   
                                                        <div class="col-xs-7">
                                                            <div class="form-group" class="col-xs-1">                                           
                                                                <input inputmode="none"  type="text" style="text-align:left" class="form-control" id="txtidcadenaColeccion" />
                                                            </div>
                                                        </div>
                                                        <div class="col-xs-1"></div>      
                                                    </div>                                                                                                   
                                                    <div class="row">
                                                        <div class="col-xs-1"></div>
                                                        <div class="col-xs-3">
                                                            <h5>Bin inicial:</h5>
                                                        </div>   
                                                        <div class="col-xs-7">
                                                            <div class="form-group" class="col-xs-1">                                           
                                                                <div id="divminimoM"></div>
                                                            </div>
                                                        </div>
                                                        <div class="col-xs-1"></div>   
                                                    </div>  
                                                    <div class="row">
                                                        <div class="col-xs-1"></div>
                                                        <div class="col-xs-3">
                                                            <h5>Bin final:</h5>
                                                        </div>   
                                                        <div class="col-xs-7">
                                                            <div class="form-group" class="col-xs-1">   
                                                                <div id="divmaximoM"></div>                                                                
                                                            </div>
                                                        </div>
                                                        <div class="col-xs-1"></div>   
                                                    </div>  
                                                    <div class="row">
                                                        <div class="col-xs-1"></div>
                                                        <div class="col-xs-3">
                                                            <h5>Forma de pago:</h5>
                                                        </div>   
                                                        <div class="col-xs-7">
                                                            <div id="tipFormaPago"></div>
                                                        </div>
                                                        <div class="col-xs-1"></div>   
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                                <div class="modal-footer">      
                                    <button type="button" class="btn btn-primary" onclick="fn_guardarCambios(2)">Aceptar</button>
                                    <button type="button" class="btn btn-default" onclick="fn_close()" data-dismiss="modal">Cancelar</button>    
                                </div>                             

                            </div>
                        </div>
                    </div>
                    <!--FIN MODAL MODIFICAR EMPRESA-->                   
                </div>
            </div>
            <!-- VARIABLES OCULTAS PARA UTILIZAR -->    
            <input inputmode="none"  type="hidden" id="idPais"/>         
            <input inputmode="none"  type="hidden" id="namePais"/>  


            <!-- LIBRERIAS Y CSS -->

            <script src="../../js/jquery1.11.1.js"></script>                 
            <script type="text/javascript" src="../../js/jquery-ui.js"></script>    
            <script language="javascript1.1"  src="../../js/alertify.js"></script>      


            <script language="javascript1.1" type="text/javascript" src="../../js/idioma.js"></script>
            <script language="javascript1.1" type="text/javascript" src="../../js/js_validaciones.js"></script>
            <script language="javascript1.1" type="text/javascript" src="../../js/ajax_datatables.js"></script>            

            <script language="javascript1.1" type="text/javascript" src="../../bootstrap/js/bootstrap.js"></script>
            <script language="javascript1.1" type="text/javascript" src="../../bootstrap/js/bootstrap-tooltip.js"></script>
            <script language="javascript1.1" type="text/javascript" src="../../bootstrap/js/bootstrap-dataTables.js"></script>
            <script type="text/javascript" src="../../bootstrap/js/inputnumber.js"></script>    
            <script type="text/javascript" src="../../bootstrap/js/moment.js"></script>           
            <script type="text/javascript" src="../../bootstrap/js/daterangepicker.js"></script>  
            <script type="text/javascript" src="../../bootstrap/js/switch.js"></script>        
            <script language="javascript1.1" type="text/javascript" src="../../js/chosen.jquery.js"></script>
            <script language="javascript1.1" type="text/javascript" src="../../js/chosen.jquery.min.js"></script>
            <script language="javascript1.1" type="text/javascript" src="../../js/chosen.proto.js"></script> 
            <script language="javascript1.1" type="text/javascript" src="../../js/chosen.proto.min.js"></script>  
            <script language="javascript1.1" type="text/javascript" src="../../js/chosen.order.jquery.min.js"></script>                        
            <script type="text/javascript" src="../../js/ajax_adminConfiguracionBines.js"></script> 


        </body>    
    </html>
<?php } ?>


