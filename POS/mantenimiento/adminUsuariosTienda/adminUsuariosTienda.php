<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////
////////DESARROLLADO POR: CHRISTIAN PINTO///////////////////////////////////////////////////////////////
///////////DESCRIPCION: ADMINISTRACION DE USUARIOS POR TIENDA, CREACION DE PERFILES CAJEROS ////////////
////////////////TABLAS: Users_Pos, Perfil_Pos //////////////////////////////////////////////////////////
////////FECHA CREACION: 27/07/2015//////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////

session_start();
include_once"../../system/conexion/clase_sql.php";
include_once"../../clases/clase_seguridades.php";
include_once"../../clases/clase_menu.php";
include_once"../../seguridades/seguridad.inc";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta http-equiv="content-type" content="text/html; charset=ISO-8859-1" />
        <title>CAJEROS</title>
        
        <link rel="stylesheet" href="../../css/jquery-ui.css" type="text/css"/>
        <link rel="stylesheet" type="text/css" href="../../css/est_administracionPantalla.css" />
        <link rel="stylesheet" type="text/css" href="../../css/alertify.core.css"/>
        <link rel="stylesheet" type="text/css" href="../../css/alertify.default.css"/>
        <link rel="stylesheet" type="text/css" href="../../bootstrap/css/bootstrap.css" />
        <link rel="stylesheet" type="text/css" href="../../css/chosen.css" />
        <link rel="stylesheet" type="text/css" href="../../bootstrap/css/daterangepicker-bs3.css" />
        <link rel="stylesheet" type="text/css" href="../../bootstrap/css/switch.css" />
        <link rel="stylesheet" type="text/css" href="../../css/est_administracionPantalla.css" />

    </head>

    <body>
        <input inputmode="none"  id="idUser" type="hidden" value="<?php echo $_SESSION['usuarioId']; ?>"/>
        <div class="superior">
            <div class="menu" style="width: 300px; margin-top: 1px;" align="center">
                <ul>
                    <li>
<!--                        <input inputmode="none"  id="btn_agregar" type="button"  onclick="fn_accionar('Nuevo')" class="botonhabilitado" value="Agregar"/>-->
                        <button id="btn_agregar" onclick="fn_accionar('Nuevo')">
                            <span>Nuevo</span>
                        </button>
                    </li>
                    <li>
<!--                        <input inputmode="none"  id="btn_restablecer" type="button" onclick="fn_restablecer();" class="botonhabilitado" value="Restablecer"/>-->
                        <button id="btn_restablecer" onclick="fn_restablecer();">
                            <span>Clave</span>
                        </button>
                    </li>
                </ul>
            </div>
            <div class="tituloPantalla">
                <h1>CAJEROS</h1>
            </div>
        </div>
        <br/>

        <div class="contenedor">
            <div class="inferior">            
                </br>
                <div class="panel panel-default" id="botonesTodos">
                    <div class="panel-heading">
                        <div class="row">
                            <div class="col-sm-8"><h5>Lista de Usuarios</h5>
                                <div id="opciones_estados" class="btn-group" data-toggle="buttons">

                                    <label class="btn btn-default btn-sm active" onclick="fn_OpcionSeleccionada('Activos');"><input inputmode="none"  id="opt_Activos" type="radio" value="Activos" checked="checked" autocomplete="off" name="estados">Activos</label>

                                    <label class="btn btn-default btn-sm" onclick="fn_OpcionSeleccionada('Inactivos');"><input inputmode="none"  id="opt_Inactivos" type="radio" value="Inactivos" autocomplete="off" name="estados">Inactivos</label>

                                    <label class="btn btn-default btn-sm" onclick="fn_OpcionSeleccionada('Todos');"><input inputmode="none"  id="opt_Todos" type="radio" value="Todos" autocomplete="off" name="estados">Todos</label>
                                </div>
                            </div>

                        </div>
                    </div>                         
                </div>
                <div id="usuriostienda">
                    <table class="table table-bordered table-hover" id="tabla_usuariostienda" border="1" cellpadding="1" cellspacing="0">
                    </table>
                </div>
            </div>
        </div>

        <!-------------------------------------INICIO MODAL NUEVO BOOTSTRAP---------------------------------------------->
        <div id="modal_user" class="modal fade" aria-hidden="true" data-backdrop="static">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 id="tqt_mdl_usr" class="modal-title"></h4>
                    </div>
                    <div class="modal-body">
                        <br/>  
                        <ul id="pestanas" class="nav nav-tabs" role="tablist">
                            <li role="presentation" id="tag_inicio" class="active"><a href="#inicio" aria-controls="inicio" role="tab" data-toggle="tab"><h5>Datos Personales</h5></a></li>
                            <li role="presentation" id="tag_maxpoint"><a href="#maxpoint" aria-controls="maxpoint" role="tab" data-toggle="tab"><h5>MaxPoint</h5></a></li>
                            <li role="presentation" id="tag_tienda"><a href="#tienda" aria-controls="tienda" role="tab" data-toggle="tab"><h5>Tiendas</h5></a></li>
                        </ul>

                        <div id="pst_cnt" class="tab-content">
                            <br/>
                            <!-- Inicio -->
                            <div role="tabpanel" class="tab-pane active" id="inicio">
                                <div class="row">
                                    <div class="col-md-9"></div>
                                    <div class="col-xs-12 col-md-2 text-right">
                                        <h6><b>Est&aacute; Activo?: <input inputmode="none"  type="checkbox" id="usr_std_id"></b></h6>
                                    </div>
                                </div>
                                <br/>
                                <div class="row">
                                    <div class="col-md-1"></div>
                                    <div class="col-xs-12 col-md-2"><h6><b>Nombre y Apellido:</b></h6></div>
                                    <div class="col-xs-12 col-md-5">
                                        <div class="form-group">
                                            <input inputmode="none"  type="text" class="form-control" id="usr_descripcion" maxlength="100"  onkeypress="return validar(event)" onchange="fn_verificarUsuario();" placeholder="Nombre y Apellido"/>
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-md-1"><h6><b>Iniciales:</b></h6></div>
                                    <div class="col-xs-12 col-md-2">
                                        <div class="form-group">
                                            <input inputmode="none"  type="text" class="form-control" id="usr_iniciales" placeholder="Iniciales"/>
                                        </div>
                                    </div>
                                    <div class="col-md-1"></div>
                                </div>
                                <div class="row">
                                    <div class="col-md-1"></div>
                                    <div class="col-xs-12 col-md-2"><h6><b>Tipo de Documento:</b></h6></div>
                                    <div class="col-xs-12 col-md-2" >
                                        <input inputmode="none"  type="checkbox" id="usr_sw_tipodocumento" name="sw" data-off-text="Pasaporte" data-on-text="C&eacute;dula" />
                                    </div>  
                                    <div class="col-md-1"></div>                                
                                    <div class="col-xs-12 col-md-4">
                                        <div class="form-group">
                                            <input inputmode="none"  type="text" class="form-control" id="usr_cedula" maxlength="15" placeholder="Documento"/>
                                        </div>
                                    </div>                                
                                    <div class="col-md-1"></div>
                                </div>                                                             
                                <div class="row">
                                    <div class="col-md-1"></div>
                                    <div class="col-xs-12 col-md-2"><h6><b>Correo Electr&oacute;nico:</b></h6></div>
                                    <div class="col-xs-12 col-md-8">
                                        <div class="form-group">
                                            <input inputmode="none"  type="text" class="form-control" maxlength="100" id="usr_email" placeholder="E-mail"/>
                                        </div>
                                    </div>
                                    <div class="col-md-1"></div>
                                </div>
                                <div class="row">
                                    <div class="col-md-1"></div>
                                    <div class="col-xs-12 col-md-2"><h6><b>Direcci&oacute;n Domicilio:</b></h6></div>
                                    <div class="col-xs-12 col-md-8">
                                        <div class="form-group">
                                            <input inputmode="none"  type="text" class="form-control" maxlength="100" id="usr_direccion" placeholder="Direcci&oacute;n del Domicilio"/>
                                        </div>
                                    </div>
                                    <div class="col-md-1"></div>
                                </div>
                                <div class="row">
                                    <div class="col-md-1"></div>
                                    <div class="col-xs-12 col-md-2"><h6><b>Telefono:</b></h6></div>
                                    <div class="col-xs-12 col-md-3">
                                        <div class="form-group">
                                            <input inputmode="none"  type="text" onkeypress="return fn_numeros(event);" maxlength="20" class="form-control" id="usr_telefono" placeholder="Telefono Personal"/>
                                        </div>
                                    </div>
                                    <div class="col-md-6"></div>
                                </div>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="maxpoint">
                                <br/>
                                <div class="row">
                                    <div class="col-md-1"></div>
                                    <div class="col-xs-12 col-md-2"><h6><b>Usuario:</b></h6></div>
                                    <div class="col-xs-12 col-md-3">
                                        <div class="form-group">
                                            <input inputmode="none"  type="text" class="form-control" id="usr_sistema" placeholder="Usuario del Sistema" onkeypress="javascript: return ValidarNumero(event,this)" />
                                        </div>
                                    </div>
                                    <div class="col-md-6"></div>
                                </div>
                                <div class="row">
                                    <div class="col-md-1"></div>
                                    <div class="col-xs-12 col-md-2"><h6><b>Perfil:</b></h6></div>
                                    <div class="col-xs-12 col-md-8">
                                        <div class="form-group">
                                            <select id="usr_prf_id" class="form-control"><option value="0">-- Seleccionar Perfil --</option></select>
                                        </div>
                                    </div>
                                    <div class="col-md-1"></div>
                                </div>
                                <div class="row">
                                    <div class="col-md-1"></div>
                                    <div class="col-xs-12 col-md-2"><h6><b>Nombre en MaxPoint:</b></h6></div>
                                    <div class="col-xs-12 col-md-8">
                                        <div class="form-group">
                                            <input inputmode="none"  type="text" onkeypress="return validar(event)" maxlength="100" class="form-control" id="usr_dscrp_mxpnt" placeholder="Nombre en MaxPoint"/>
                                        </div>
                                    </div>
                                    <div class="col-md-1"></div>
                                </div>
<!--                                <div class="row">
                                    <div class="col-md-1"></div>
                                    <div class="col-xs-12 col-md-2"><h6><b>Clave:</b></h6></div>
                                    <div class="col-xs-12 col-md-8">
                                        <div class="form-group">
                                            <input inputmode="none"  type="password" class="form-control" id="usr_clave" placeholder="Clave" onkeypress="return fn_numeros(event);" maxlength="10"/>
                                        </div>
                                    </div>
                                    <div class="col-md-1"></div>
                                </div>-->
                                <div class="row">
                                    <div class="col-md-1"></div>
                                    <div class="col-xs-12 col-md-2"><h6><b>Fecha de Inicio:</b></h6></div>
                                    <div class="col-xs-12 col-md-3">
                                        <div class="form-group">
                                            <div class="input-prepend input-group">
                                                <span class="add-on input-group-addon"><i class="glyphicon glyphicon-calendar fa fa-calendar"></i></span>
                                                <input inputmode="none"  type="text" value="" readonly="readonly" class="form-control" name="usr_fin" id="usr_inicio" placeholder="Fecha Inicio"/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-md-2"><h6><b>Fecha de Fin:</b></h6></div>
                                    <div class="col-xs-12 col-md-3">
                                        <div class="form-group">
                                            <div class="input-prepend input-group">
                                                <span class="add-on input-group-addon"><i class="glyphicon glyphicon-calendar fa fa-calendar"></i></span>
                                                <input inputmode="none"  type="text" value="" onkeypress="return fn_numeros(event);" class="form-control" name="usr_fin" id="usr_fin" placeholder="Fecha Fin"/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-1"></div>
                                </div>
                                <div class="row">
                                    <div class="col-md-1"></div>
                                    <div class="col-xs-12 col-md-2"><h6><b>Tarjeta:</b></h6></div>
                                    <div class="col-xs-12 col-md-8">
                                        <div class="form-group">
                                            <input inputmode="none"  type="password" class="form-control" maxlength="30" id="usr_tarjeta" placeholder="Tarjeta"/>
                                        </div>
                                    </div>
                                    <div class="col-md-1"></div>
                                </div>
                            </div>

                            <!-- Tienda -->
                            <div role="tabpanel" class="tab-pane" id="tienda">
                                <br/>
                                <div class="row">
                                    <div class="col-md-2"></div>
                                    <div class="col-xs-12 col-md-6"><h6><b>Tienda:</b></h6></div>                                    
                                    <div class="col-md-2"></div>
                                </div>
                                <div class="row">
                                    <div class="col-md-2"></div>
                                    <div class="col-xs-12 col-md-8">
                                        <div id="pcns_rst_lclzcn" class="btn-group" data-toggle="buttons">
                                            <label id="pcn_rst_lclzcn1" class="btn btn-default btn-sm active" onclick="fn_cargarLocales(0);"><input inputmode="none"  type="radio" value="Todos" autocomplete="off" name="ptns_std_prfl" checked="checked">Todos</label>
                                            <label class="btn btn-default btn-sm" onclick="fn_cargarLocales(1);"><input inputmode="none"  type="radio" value="Activos" autocomplete="off"  name="ptns_std_prfl">Quito</label>
                                            <label class="btn btn-default btn-sm" onclick="fn_cargarLocales(2);"><input inputmode="none"  type="radio" value="Inactivos" autocomplete="off"  name="ptns_std_prfl">Guayaquil</label>
                                        </div>
                                    </div>
                                    <div class="col-md-2"></div>
                                </div>
                                <br/>
                                <div class="row">
                                    <div class="col-md-2"></div>
                                    <div class="col-xs-12 col-md-8">
                                        <div style="height: 260px; overflow-y: auto;">
                                            <div id="lst_rst_usr" class="list-group"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-2"></div>
                                </div>
                            </div>
                        </div>
                        <h6 id="advertenciaUsuario" style="color: #d93f3f; margin-left: 25px;">* Campos Obligatorios</h6>                        
                    </div>
                    <div id="btn_pcn_dmn" class="modal-footer"></div>
                </div>
            </div>
        </div>
        
        <!-------------------------------------INICIO MODAL NUEVO BOOTSTRAP---------------------------------------------->
        <div class="modal fade" id="modal_cambioclave" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header panel-footer">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button> 
                        <h4 class="modal-title" id="titulomodalclave">Cambio de Clave </b></h4>
                    </div>

                    </br>
                    <div class="modal-body"> 
                        <div class="row">
                            <div class="col-xs-1"></div>
                            <div class="col-xs-4">
                                <h5>Ingrese Nueva Clave:</h5>
                            </div>
                            <div class="col-xs-6">
                                <div class="form-group" class="col-xs-1"> 
                                    <input inputmode="none"  type="password" style="text-align:center;" maxlength="10" class="form-control" id="usr_clave_cambio" onkeypress="return fn_numeros(event);"/>                    	
                                </div>
                            </div>
                            <div class="col-xs-1"></div>
                        </div>                       
                    </div>
                    <div class="modal-footer panel-footer">
                        <button type="button" class="btn btn-primary" onclick="fn_restablecer();" >Aceptar</button>
                        <button type="button" class="btn btn-default" onclick="" data-dismiss="modal">Cancelar</button>

                    </div>
                </div>
            </div> 
        </div> 
        
        <input inputmode="none"  type="hidden" id="txt_idusuario"/>
        <input inputmode="none"  type="hidden" id="txt_nombreusuario"/>
        
        <script src="../../js/jquery1.11.1.js"></script>
        <script language="javascript1.1" type="text/javascript" src="../../js/ajax_datatables.js"></script>
        <script language="javascript1.1" type="text/javascript" src="../../bootstrap/js/bootstrap.js"></script>
        <script language="javascript1.1" type="text/javascript" src="../../bootstrap/js/bootstrap-dataTables.js"></script>
        <script language="javascript1.1"  src="../../js/alertify.js"></script>
        <script type="text/javascript" src="../../js/ajax_adminusuariostienda.js"></script>
        <script type="text/javascript" src="../../js/ajax_ValidaDocumento.js"></script>
        <script language="javascript1.1" type="text/javascript" src="../../js/js_validaciones.js"></script>
        <script language="javascript1.1" type="text/javascript" src="../../js/chosen.jquery.js"></script>
        <script language="javascript1.1" type="text/javascript" src="../../js/chosen.jquery.min.js"></script>
        <script language="javascript1.1" type="text/javascript" src="../../js/chosen.proto.js"></script>
        <script language="javascript1.1" type="text/javascript" src="../../js/chosen.proto.min.js"></script>
        <script type="text/javascript" src="../../bootstrap/js/query.bootstrap-growl.min.js"></script>
        <script type="text/javascript" src="../../bootstrap/js/inputnumber.js"></script>
        <script type="text/javascript" src="../../bootstrap/js/moment.js"></script>
        <script type="text/javascript" src="../../bootstrap/js/daterangepicker.js"></script>
        <script type="text/javascript" src="../../bootstrap/js/switch.js"></script>

    </body>
</html>