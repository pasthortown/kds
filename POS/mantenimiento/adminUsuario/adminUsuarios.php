<?php
session_start();

///////////////////////////////////////////////////////////////////////////////
///////DESARROLLADOR: Jorge Tinoco ////////////////////////////////////////////
///////DESCRIPCION: Pantalla de configuraci�n de usuarios /////////////////////
///////FECHA CREACION: 28-01-2016 /////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////

include_once"../../system/conexion/clase_sql.php";
include_once"../../clases/clase_seguridades.php";
include_once"../../seguridades/seguridad_niv3.inc";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="content-type" content="text/html; charset=ISO-8859-1" /> 
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /> 
        <title>Administraci&oacute;n</title>

        <!-- ESTILOS -->
        <link rel="stylesheet" type="text/css" href="../../bootstrap/css/bootstrap.css" />
        <link rel="stylesheet" type="text/css" href="../../css/alertify.core.css" />
        <link rel="stylesheet" type="text/css" href="../../css/alertify.default.css" />
        <link rel="stylesheet" type="text/css" href="../../bootstrap/css/daterangepicker-bs3.css" />
        <link rel="stylesheet" type="text/css" href="../../bootstrap/css/switch.css" />
        <link rel="stylesheet" type="text/css" href="../../css/est_administracionPantalla.css" />

    </head>
    <body>

        <input inputmode="none"  id="sess_usr_id" type="hidden" value="<?php echo $_SESSION['usuarioId']; ?>"/>
        <input inputmode="none"  id="sess_cdn_id" type="hidden" value="<?php echo $_SESSION['cadenaId']; ?>"/> 
        <input inputmode="none"  id="restaurante" type="hidden" value="<?php echo $_SESSION['rstId']; ?>"/>

        <div class="superior">
            <div class="menu" style="width: 300px;">
                <ul>
                    <li>
                        <button id="btn_agregar" onclick="fn_agregar()">
                            <span>Nuevo</span>
                        </button>
                    </li>    
                    <li>
                        <button id="btn_restablecer" onclick="fn_modalCambioClave()">
                            <span>Clave</span>
                        </button>
                    </li>
                </ul>
            </div>
            <div class="tituloPantalla">
                <h1>Usuarios</h1>
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
                                <h5 id="claveNueva">Ingrese la nueva clave:</h5>
                            </div>
                            <div class="col-xs-6">
                                <div class="form-group" class="col-xs-1"> 
                                    <input inputmode="none"  type="password" style="text-align:center;" maxlength="10" class="form-control" id="usr_clave_cambio1" onkeypress="return fn_numeros(event);"/>                    	
                                </div>
                            </div>
                            <div class="col-xs-1"></div>
                        </div>    
                        <div class="row">
                            <div class="col-xs-1"></div>
                            <div class="col-xs-4">
                                <h5 id="claveConfirma">Confirme su clave:</h5>
                            </div>
                            <div class="col-xs-6">
                                <div class="form-group" class="col-xs-1"> 
                                    <input inputmode="none"  type="password" style="text-align:center;" maxlength="10" class="form-control" id="usr_clave_cambio2" onkeypress="return fn_numeros(event);"/>                    	
                                </div>
                            </div>
                            <div class="col-xs-1"></div>
                        </div>    
                        <h6 id="advertencia" style="color: #d93f3f; left: 0;">*Campos Obligatorios</h6>
                    </div>
                    <div class="modal-footer panel-footer">
                        <button type="button" class="btn btn-primary" onclick="fn_validarCredenciales();" >Aceptar</button>
                        <button type="button" class="btn btn-default" onclick="" data-dismiss="modal">Cancelar</button>
                    </div>
                </div>
            </div> 
        </div>

        <div class="contenedor">        
            <div class="inferior">			
                <div class="panel panel-default">            
                    <div class="panel-heading">
                        <h3 id="tqt_mdl" class="panel-title">Lista de Usuarios</h3>
                        <br/>
                        <div id="opciones_estado" class="btn-group" data-toggle="buttons">
                            <label id="opciones_1" class="btn btn-default btn-sm active" onclick="fn_cargarUsuarios(1, 'Activo')">
                                <input inputmode="none"  type="radio" value="Activos" autocomplete="off"  name="ptns_std_prfl" checked="checked"/>Activos
                            </label>
                            <label class="btn btn-default btn-sm" onclick="fn_cargarUsuarios(1, 'Inactivo')">
                                <input inputmode="none"  type="radio" value="Inactivos" autocomplete="off"  name="ptns_std_prfl"/>Inactivos
                            </label>
                            <label onclick="fn_cargarUsuarios(0, 'Todos')" class="btn btn-default btn-sm">
                                <input inputmode="none"  type="radio" value="Todos" autocomplete="off" name="ptns_std_prfl"/>Todos
                            </label>
                        </div>
                    </div>            
                    <div class="panel-body">
                        <center>
                            <table class="table table-bordered table-hover" id="tbl_lst_srs"></table>
                        </center>
                    </div>                
                    <div class="panel-footer"></div>
                </div>  
            </div>  
        </div>  

        <!-- Modal Usuario -->
        <div id="mdl_usr" class="modal fade" aria-hidden="true" data-backdrop="static">
            <div class="modal-dialog modal-lg" style="width:1100px; margin: 5px auto;">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 id="tqt_mdl_usr" class="modal-title"></h4>
                    </div>
                    <div class="modal-body">
                        <br/>             
                        <!-- Nav tabs -->
                        <ul id="pestanas" class="nav nav-tabs" role="tablist">
                            <li role="presentation" id="tag_inicio" class="active"><a href="#inicio" aria-controls="inicio" role="tab" data-toggle="tab"><h5>Datos Personales</h5></a></li>
                            <li role="presentation" id="tag_maxpoint"><a href="#maxpoint" aria-controls="maxpoint" role="tab" data-toggle="tab"><h5>MaxPoint</h5></a></li>
                            <li role="presentation" id="tag_tienda"><a href="#tienda" aria-controls="tienda" role="tab" data-toggle="tab"><h5>Tiendas</h5></a></li>
                            <li role="presentation" id="tag_politicas"><a href="#politicas" aria-controls="politicas" role="tab" data-toggle="tab"><h5>Pol&iacute;ticas de Configuraci&oacute;n</h5></a></li>
                        </ul>

                        <!-- Tab panes -->
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
                                <div class="row">
                                    <div class="col-md-1"></div>
                                    <div class="col-xs-12 col-md-2"><h6><b>Nombre y Apellido:</b></h6></div>
                                    <div class="col-xs-12 col-md-5">
                                        <div class="form-group">
                                            <input inputmode="none"  type="text" class="form-control" id="usr_descripcion" onchange="fn_verificarUsuario()" placeholder="Nombre y Apellido" onkeypress="return validarCaracteres(event)"
                                                   />
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-md-1"><h6><b>Iniciales:</b></h6></div>
                                    <div class="col-xs-12 col-md-2">
                                        <div class="form-group">
                                            <input inputmode="none"  type="text" class="form-control" id="usr_iniciales" placeholder="Iniciales">
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
                                            <input inputmode="none"  type="text" class="form-control" id="usr_cedula" placeholder="Documento" />
                                        </div>
                                    </div>                                
                                    <div class="col-md-1"></div>
                                </div>                           
                                <div class="row">
                                    <div class="col-md-1"></div>
                                    <div class="col-xs-12 col-md-2"><h6><b>Correo Electr&oacute;nico:</b></h6></div>
                                    <div class="col-xs-12 col-md-8">
                                        <div class="form-group">
                                            <input inputmode="none"  type="text" class="form-control" id="usr_email" placeholder="E-mail">
                                        </div>
                                    </div>
                                    <div class="col-md-1"></div>
                                </div>
                                <div class="row">
                                    <div class="col-md-1"></div>
                                    <div class="col-xs-12 col-md-2"><h6><b>Direcci&oacute;n Domicilio:</b></h6></div>
                                    <div class="col-xs-12 col-md-8">
                                        <div class="form-group">
                                            <input inputmode="none"  type="text" class="form-control" id="usr_direccion" placeholder="Direcci&oacute;n del Domicilio">
                                        </div>
                                    </div>
                                    <div class="col-md-1"></div>
                                </div>
                                <div class="row">
                                    <div class="col-md-1"></div>
                                    <div class="col-xs-12 col-md-2"><h6><b>Telefono:</b></h6></div>
                                    <div class="col-xs-12 col-md-3">
                                        <div class="form-group">
                                            <input inputmode="none"  type="text" class="form-control" id="usr_telefono" placeholder="Telefono Personal">
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
                                            <input inputmode="none"  type="text" class="form-control" id="usr_sistema" placeholder="Usuario del Sistema" />
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
                                <div id="campoClave" class="row">
                                    <div class="col-md-1"></div>
                                    <div class="col-xs-12 col-md-2"><h6><b>Clave:</b></h6></div>
                                    <div class="col-xs-12 col-md-8">
                                        <div class="form-group">
                                            <input inputmode="none"  type="password" class="form-control" id="usr_clave" placeholder="Clave" onkeypress="return fn_numeros(event);" maxlength="10"/>
                                        </div>
                                    </div>
                                    <div class="col-md-1"></div>
                                </div>
                                <div class="row">
                                    <div class="col-md-1"></div>
                                    <div class="col-xs-12 col-md-2"><h6><b>Nombre en MaxPoint:</b></h6></div>
                                    <div class="col-xs-12 col-md-8">
                                        <div class="form-group">
                                            <input inputmode="none"  type="text" class="form-control" id="usr_dscrp_mxpnt" placeholder="Nombre en MaxPoint">
                                        </div>
                                    </div>
                                    <div class="col-md-1"></div>
                                </div>                          

                                <div class="row">
                                    <div class="col-md-1"></div>
                                    <div class="col-xs-12 col-md-2"><h6><b>Fecha de Inicio:</b></h6></div>
                                    <div class="col-xs-12 col-md-3">
                                        <div class="form-group">
                                            <div class="input-prepend input-group">
                                                <span class="add-on input-group-addon"><i class="glyphicon glyphicon-calendar fa fa-calendar"></i></span>
                                                <input inputmode="none"  type="text" value="" class="form-control" name="usr_fin" id="usr_inicio" placeholder="Fecha Inicio"/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-md-2"><h6><b>Fecha de Fin:</b></h6></div>
                                    <div class="col-xs-12 col-md-3">
                                        <div class="form-group">
                                            <div class="input-prepend input-group">
                                                <span class="add-on input-group-addon"><i class="glyphicon glyphicon-calendar fa fa-calendar"></i></span>
                                                <input inputmode="none"  type="text" value="" class="form-control" name="usr_fin" id="usr_fin" placeholder="Fecha Fin"/>
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
                                            <input inputmode="none"  type="password" class="form-control" maxlength="30" id="usr_tarjeta" placeholder="Tarjeta" />
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
                                    <div class="col-md-2 text-right">Marcar Todos &nbsp <input inputmode="none"  id="chck_rst_tds" name="chck_rst_tds" onclick="fn_seleccionarTodosLocales()" value="1" type="checkbox"></div>
                                    <div class="col-md-2"></div>
                                </div>
                                <div class="row">
                                    <div class="col-md-2"></div>
                                    <div class="col-xs-12 col-md-8">
                                        <div id="pcns_rst_lclzcn" class="btn-group" data-toggle="buttons">
                                        </div>
                                    </div>
                                    <div class="col-md-2"></div>
                                </div>
                                <br/>
                                <div class="row">
                                    <div class="col-md-2"></div>
                                    <div class="col-xs-12 col-md-8">
                                        <!-- Carga Locales -->
                                        <div style="height: 200px; overflow-y: auto;">
                                            <div id="lst_rst_usr" class="list-group"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-2"></div>
                                </div>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="politicas">

                                <div class="panel panel-default" style="width:1060px;" align="center">
                                    <!-- Default panel contents -->
                                    <div class="panel-heading">
                                        <div class="row">
                                            <div class="col-xs-10 col-md-9"><h6><b>USUARIOS COLECCI&Oacute;N DE DATOS</b></h6></div>
                                            <div class="col-md-1"></div>
                                            <div>
                                                <button type="button" class="btn btn-default" onclick="agregarColeccionUsuario();" >
                                                    <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
                                                </button>
                                                <button type="button" class="btn btn-default" onclick="editarColeccionUsuario();">
                                                    <span class="glyphicon glyphicon-pencil" style="opacity: none;" aria-hidden="true"></span>
                                                </button>
                                            </div>
                                        </div>
                                        <!-- TABLA DETALLE COLECCECION USUARIOS -->
                                        <table id="cabeceraDatos" class="table table-bordered bg-primary" style="font-size: 12px;">
                                            <thead>
                                                <tr>
                                                    <th class="bg-primary text-center" style="width: 150px;"><label>Descripci&oacute;n</label></th>
                                                    <th class="bg-primary text-center" style="width: 150px;"><label>Par&aacute;metro</label></th>
                                                    <th class="bg-primary text-center" style="width: 70px;"><label>Espec&iacute;fica Valor</label></th>
                                                    <th class="bg-primary text-center" style="width: 70px;"><label>Obligatorio</label></th>
                                                    <th class="bg-primary text-center" style="width: 70px;"><label>Tipo De Dato</label></th>
                                                    <th class="bg-primary text-center" style="width: 300px;"><label>Dato</label></th>
                                                    <th class="bg-primary text-center" style="width: 70px;"><label>Activo</label></th>                                                    
                                                </tr>
                                            </thead>
                                        </table>
                                        <div align="center" style="height: 200px; overflow-x: auto; overflow-y: auto;">
                                            <div class="form-group" id="detalleUsuariosColeccion">
                                                <table id="tablaUsuariosColeccion" class="table table-bordered table-fixed-layout" style="font-size: 11px;"></table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <h6 id="advertenciaUsuario" style="color: #d93f3f; margin-left: 25px;">* Campos Obligatorios</h6>
                    </div>
                    <div id="btn_pcn_dmn" class="modal-footer"></div>
                </div>
            </div>
        </div>

        <!--Modal Coleccion de datos usuario-->
        <?php
        include_once "adminColeccionUsuarios.php";
        ?>

        <input inputmode="none"  type="hidden" id="hdn_usr_id" value=""/>
        <input inputmode="none"  type="hidden" id="hdn_tipodocumento" />

        <!-- JavaScript -->
        <script type="text/javascript" src="../../js/jquery1.11.1.js"></script>
        <script type="text/javascript" src="../../bootstrap/js/moment.js"></script>
        <script type="text/javascript" src="../../bootstrap/js/daterangepicker.js"></script>
        <script type="text/javascript" src="../../js/ajax_datatables.js"></script>
        <script type="text/javascript" src="../../bootstrap/js/bootstrap.js"></script>
        <script type="text/javascript" src="../../bootstrap/js/bootstrap-dataTables.js"></script>
        <script language="javascript1.1"  src="../../js/alertify.js"></script>
        <script type="text/javascript" src="../../bootstrap/js/inputnumber.js"></script>
        <script type="text/javascript" src="../../bootstrap/js/switch.js"></script>
        <script type="text/javascript" src="../../js/ajax_ValidaDocumento.js"></script>
        <script type="text/javascript" src="../../js/ajax_admusuarios.js"></script>
        <!-- Valida que solo ingresen números -->
        <script language="javascript1.1" type="text/javascript" src="../../js/js_validaciones.js"></script>
        <script> indice = 1;</script>

        <script type="text/javascript" src="../../js/ajax_adminColeccionDeDatosUsuarios.js"></script>

    </body>
</html>