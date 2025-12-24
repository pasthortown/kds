<?php
session_start();

ini_set("memory_limit", "256M");
header("Expires: Fri, 25 Dec 1980 00:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");

///////////////////////////////////////////////////////////////////////////////
///////DESARROLLADOR: Jorge Tinoco ////////////////////////////////////////////
///////DESCRIPCION: Configuraci�n de Pantallas ////////////////////////////////
///////FECHA CREACION: 25-01-2016 /////////////////////////////////////////////
///////FECHA DE MODIFICACION: 22-12-2016 //////////////////////////////////////
///////USUARIO QUE MODIFICO: Juan Estevez /////////////////////////////////////
///////DESCRIPCION DEL ULTIMO CAMBIO: Creacion de panel ///////////////////////
///////////////////////////////////////////////////////////////////////////////

include_once '../../system/conexion/clase_sql.php';
include_once '../../clases/clase_seguridades.php';
include_once '../../seguridades/seguridad_niv3.inc';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="content-type" content="text/html; charset=ISO-8859-1" />
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

        <title>Administraci&oacute;n</title>

        <!-- ESTILOS -->
        <link rel="stylesheet" type="text/css" href="../../bootstrap/css/bootstrap.min.css" />
        <link rel="stylesheet" type="text/css" media="all" href="../../bootstrap/css/daterangepicker-bs3.css" />
        <link rel="stylesheet" type="text/css" href="../../bootstrap/css/switch.css" />
        <link rel="stylesheet" type="text/css" href="../../css/chosen.css" />
        <link rel="stylesheet" type="text/css" href="../../bootstrap/css/bootstrap-editable.css" />
        <link rel="stylesheet" type="text/css" href="../../css/alertify.core.css" />
        <link rel="stylesheet" type="text/css" href="../../css/alertify.default.css" />
        <link rel="stylesheet" type="text/css" href="../../css/est_administracionPantalla.css" />
        <link rel="stylesheet" type="text/css" media="all" href="../../bootstrap/css/daterangepicker-bs3.css" />
        <link rel="stylesheet" type="text/css" href="../../bootstrap/css/switch.css" />

        <style>
            .btn-file {
                position: relative;
                overflow: hidden;
            }

            .btn-file input[type=file] {
                position: absolute;
                top: 0;
                right: 0;
                min-width: 100%;
                min-height: 100%;
                font-size: 100px;
                text-align: right;
                filter: alpha(opacity=0);
                opacity: 0;
                outline: none;
                background: white;
                cursor: inherit;
                display: block;
            }
        </style>
    </head>
    <body>
        <input inputmode="none"  id="sess_usr_id" type="hidden" value="<?php echo $_SESSION['usuarioId']; ?>"/>
        <input inputmode="none"  id="sess_cdn_id" type="hidden" value="<?php echo $_SESSION['cadenaId']; ?>"/>
        <div class="superior">
            <div class="menu" align="center" style="width: 400px;">
                <ul>
                    <li>
                        <button id="btnSincronizarRestaurantes" class="botonMnSpr l-basic-elaboration-briefcase-download" onclick="sincronizarRestaurantes()">
                            <span>Obtener</span>
                        </button>
                    </li>
                </ul>
            </div>
            <div class="tituloPantalla">
                <h1>Restaurantes</h1>
            </div>
        </div>
        <div class="contenedor">
            <div class="inferior">
                <div id="prb_img" class="row" style="display: none;">
                    <div class="col-sm-6">
                        <canvas height="300px" width="300px" id="micanvas"></canvas>
                    </div>
                    <div class="col-sm-6">
                        <textarea id="txt_area_imagen"></textarea>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <div class="row">
                            <div class="col-sm-8"><h5>Lista de Restaurantes</h5>
                                <div id="opciones_estado" class="btn-group" data-toggle="buttons">
                                    <label id="opciones_1" class="btn btn-default btn-sm active">
                                        <input inputmode="none"  type="radio" value="Activos" autocomplete="off" name="ptns_std_rst" onchange="fn_consultarListaRestaurantesActivos(0)" checked="checked" />Activos
                                    </label>
                                    <label class="btn btn-default btn-sm">
                                        <input inputmode="none"  type="radio" value="Inactivos" autocomplete="off" name="ptns_std_rst" onchange="fn_consultarListaRestaurantesInactivos(0)" />Inactivos
                                    </label>
                                    <label class="btn btn-default btn-sm">
                                        <input inputmode="none"  type="radio" value="Todos" autocomplete="off" name="ptns_std_rst" onchange="fn_consultarListaRestaurantes(0)" />Todos
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-8">
                            <h5></h5>
                        </div>
                        <div class="col-sm-4"></div>
                    </div>
                    <div class="panel-body">
                        <table id="listaRestaurantes" class="table table-bordered table-hover"></table>
                    </div>
                </div>
            </div> <!-- Fin Contenedor Inferior -->
        </div>  <!-- Fin Contenedor -->

        <!-- Modal Pos Plus -->
        <div class="modal fade " id="modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-keyboard="false" data-backdrop="static">
            <div class="modal-dialog" style="width:1100px;">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 id="titulomodal" class="modal-title"></h4>
                    </div>
                    <!--            <div class="modal-body">-->
                    <div role="tabpanel">
                        <!-- Nav tabs -->
                        <ul id="pestanas" class="nav nav-tabs" role="tablist">
                            <li role="presentation" id="tag_informacion"><a href="#informacion" aria-controls="informacion" role="tab" data-toggle="tab"><h5>Sistema Gerente Web</h5></a></li>
                            <li role="presentation" id="tag_fiscal"><a href="#fiscal" aria-controls="fiscal" role="tab" data-toggle="tab"><h5>Informaci&oacute;n Fiscal</h5></a></li>
                            <li role="presentation" id="tag_inicio"><a href="#inicio" aria-controls="inicio" role="tab" data-toggle="tab"><h5>MaxPoint</h5></a></li>
                            <li role="presentation" id="tag_pisos"><a href="#pisos" aria-controls="pisos" role="tab" data-toggle="tab"><h5>Pisos y Areas</h5></a></li>
                            <li role="presentation" id="tag_autofacturas"><a href="#autofacturas" aria-controls="autofacturas" role="tab" data-toggle="tab"><h5>Autoimpresores</h5></a></li>
                            <li role="presentation" id="tag_coleccion"><a href="#coleccion_rest" aria-controls="coleccion_rest" role="tab" data-toggle="tab"><h5>Politicas de Configuraci&oacute;n</h5></a></li>
                            <li role="presentation" id="tag_documentoimpresion"><a href="#coleccion_documentoimpresion" aria-controls="coleccion_documentoimpresion" role="tab" data-toggle="tab"><h5>Archivos de Impresi&oacute;n</h5></a></li>
                        </ul>
                        <!-- Tab panel -->
                        <div id="contenedor_gestion" class="tab-content">
                            <!-- Informacion -->
                            <div role="tabpanel" class="tab-pane active" id="informacion">
                                <br/><br/>
                                <div class="row">
                                    <div class="col-md-8"></div>
                                    <div class="col-xs-12 col-md-4">
                                        <div class="btn-group">
                                            <h5 class="text-right"><b>Est&aacute; Activo?: <input inputmode="none"  type="checkbox" value="1" id="rst_std_id"></b></h5>
                                        </div>
                                    </div>
                                    <div class="col-md-1"></div>
                                </div>
                                <div class="row">
                                    <div class="col-md-2"></div>
                                    <div class="col-xs-12 col-md-2"><h6><b>Categoria de Precios:</b></h6></div>
                                    <div class="col-xs-12 col-md-4">
                                        <div class="form-group">
                                            <select class="form-control" id="cat_rst"></select>
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-md-4"></div>
                                </div>
                                <div class="row">
                                    <div class="col-md-2"></div>
                                    <div class="col-xs-12 col-md-2"><h6><b>Ciudad:</b></h6></div>
                                    <div class="col-xs-12 col-md-4">
                                        <div class="form-group">
                                            <select class="form-control" id="ciu_rst"></select>
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-md-4"></div>
                                </div>
                                <div class="row">
                                    <div class="col-md-2"></div>
                                    <div class="col-xs-12 col-md-2"><h6><b>Tipo de Servicio:</b></h6></div>
                                    <div class="col-xs-12 col-md-4">
                                        <div class="form-group">
                                            <select class="form-control" id="tpsrv_rst"></select>
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-md-4"></div>
                                </div>
                                <div class="row">
                                    <div class="col-md-2"></div>
                                    <div class="col-xs-12 col-md-2"><h6><b>MID:</b></h6></div>
                                    <div class="col-xs-12 col-md-4">
                                        <div class="form-group">
                                            <input inputmode="none"  type="text" class="form-control" id="rst_mid" placeholder="MID Tarjeta de Credito" />
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-md-4"></div>
                                </div>
                                <div class="row">
                                    <div class="col-md-2"></div>
                                    <div class="col-xs-12 col-md-2"><h6><b>Localizaci&oacute;n:</b></h6></div>
                                    <div class="col-xs-12 col-md-8"><h5 id="lclzcn_rst"></h5></div>
                                </div>
                            </div>

                            <!-- Fiscal -->
                            <div role="tabpanel" class="tab-pane" id="fiscal">
                                <br/><br/>
                                <div class="row">
                                    <div class="col-md-1"></div>
                                    <div class="col-xs-12 col-md-2"><h6><b>Direcci&oacute;n:</b></h6></div>
                                    <div class="col-xs-12 col-md-8">
                                        <div class="form-group">
                                            <input inputmode="none"  type="text" class="form-control" id="rst_direccion" placeholder="Direcci&oacute;n del Local" />
                                        </div>
                                    </div>
                                    <div class="col-md-1"></div>
                                </div>
                                <div class="row">
                                    <div class="col-md-1"></div>
                                    <div class="col-xs-12 col-md-2"><h6><b>Tel&eacute;fono:</b></h6></div>
                                    <div class="col-xs-12 col-md-4">
                                        <div class="form-group">
                                            <input inputmode="none"  type="text" class="form-control" id="rst_telefono" placeholder="Telefono" />
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-md-5"></div>
                                    <div class="col-md-1"></div>
                                </div>
                                <div class="row">
                                    <div class="col-md-1"></div>
                                    <div class="col-xs-12 col-md-2"><h6><b>M&eacute;todo de Impuesto:</b></h6></div>
                                    <div class="col-xs-12 col-md-8">
                                        <div class="form-group">
                                            <select class="form-control" id="tp_mtd_mpst_rst" onchange="fn_validarImpuestosIE()"></select>
                                        </div>
                                    </div>
                                    <div class="col-md-1"></div>
                                </div>
                                <div class="row">
                                    <div class="col-md-1"></div>
                                    <div class="col-xs-12 col-md-2"><h6><b>Impuestos:</b></h6></div>
                                    <div class="col-xs-12 col-md-8">
                                        <div class="form-group">
                                            <select data-placeholder="Seleccionar Impuestos" multiple="multiple" id="slct_mpsts_rstrnt" class="chosen-select" onchange="fn_validarImpuestosLocal()"></select>
                                        </div>
                                    </div>
                                    <div class="col-md-1"></div>
                                </div>
                                <div class="row">
                                    <div class="col-md-1"></div>
                                    <div class="col-xs-12 col-md-2"><h6><b>Tipo Facturaci&oacute;n:</b></h6></div>
                                    <div class="col-xs-12 col-md-8">
                                        <div class="form-group">
                                            <select class="form-control" id="tpfct_rst"></select>
                                        </div>
                                    </div>
                                    <div class="col-md-1"></div>
                                </div>
                                <div class="row">
                                    <div class="col-md-1"></div>
                                    <div class="col-xs-12 col-md-2"><h6><b>N&uacute;mero de Serie:</b></h6></div>
                                    <div class="col-xs-12 col-md-3">
                                        <div class="form-group">
                                            <input inputmode="none"  type="text" class="form-control" id="rst_nmr_sr" placeholder="Serie" />
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-md-2"><h6><b>Punto de Emisi&oacute;n:</b></h6></div>
                                    <div class="col-xs-12 col-md-3">
                                        <div class="form-group">
                                            <input inputmode="none"  type="text" class="form-control" id="rst_pnt_msn" placeholder="Punto de Emisi&oacute;n" />
                                        </div>
                                    </div>
                                    <div class="col-md-1"></div>
                                </div>
                            </div>

                            <!-- Inicio -->
                            <div role="tabpanel" class="tab-pane" id="inicio">
                                <br/><br/>
                                <div class="row">
                                    <div class="col-md-2"></div>
                                    <div class="col-xs-12 col-md-2"><h6><b>Tiempo de anulaci&oacute;n (Minutos):</b></h6></div>
                                    <div class="col-xs-12 col-md-2">
                                        <div class="input-prepend input-group number-spinner">
                                            <span class="add-on input-group-addon btn btn-primary" data-dir="dwn"><i class="glyphicon glyphicon-minus fa fa-calendar"></i></span>
                                            <input inputmode="none"  id="rst_tmp_pdd" type="text" class="form-control text-center" value="0" min="0" max="50" />
                                            <span class="add-on input-group-addon btn btn-primary" data-dir="up"><i class="glyphicon glyphicon-plus fa fa-calendar"></i></span>
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-md-6"></div>
                                </div>
                                <br/>
                                <div class="row">
                                    <div class="col-md-2"></div>
                                    <div class="col-xs-12 col-md-2"><h6><b>Cancelar Pago:</b></h6></div>
                                    <div class="col-xs-12 col-md-2">
                                        <input inputmode="none"  type="checkbox" id="rst_cnclr_pg" data-off-text="No" data-on-text="Si" />
                                    </div>
                                    <div class="col-xs-12 col-md-2"><h6><b>Solicitar N&uacute;mero de Personas:</b></h6></div>
                                    <div class="col-xs-12 col-md-2">
                                        <input inputmode="none"  type="checkbox" id="rst_nmr_prsns" data-off-text="No" data-on-text="Si" />
                                    </div>
                                    <div class="col-md-2"></div>
                                </div>
                                <br/>
                                <div class="row">
                                    <div class="col-md-2"></div>
                                    <div class="col-xs-12 col-md-2"><h6><b>Abrir Cajon (Final Transaccion):</b></h6></div>
                                    <div class="col-xs-12 col-md-2">
                                        <input inputmode="none"  type="checkbox" id="rst_br_cjn" data-off-text="No" data-on-text="Si" />
                                    </div>
                                    <div class="col-xs-12 col-md-2"><h6><b>Producto al Peso (gramos):</b></h6></div>
                                    <div class="col-xs-12 col-md-2">
                                        <input inputmode="none"  type="checkbox" id="rst_cntd_grms" data-off-text="No" data-on-text="Si" />
                                    </div>
                                </div>
                                <br/>
                                <div class="row">
                                    <div class="col-md-2"></div>
                                    <div class="col-xs-12 col-md-2"><h6><b>Atenci&oacute;n 24 Horas:</b></h6></div>
                                    <div class="col-xs-12 col-md-2">
                                        <input inputmode="none"  type="checkbox" id="rst_sw_atencion24horas" data-off-text="No" data-on-text="Si" />
                                    </div>  
                                    <div class="col-xs-12 col-md-6"></div>
                                </div>
                                <br/>
                            </div>

                            <!-- Pisos -->
                            <div role="tabpanel" class="tab-pane" id="pisos">
                                <br/><br/>
                                <div class="row">
                                    <div class="col-md-1"></div>
                                    <div class="col-xs-12 col-md-10">
                                        <button type="button" class="btn btn-default" aria-label="Left Align" onclick="fn_agregarPiso()">
                                            <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Agregar Piso
                                        </button>
                                    </div>
                                    <div class="col-md-1"></div>
                                </div>
                                <br/>
                                <div class="row">
                                    <div class="col-md-1"></div>
                                    <div class="col-xs-12 col-md-10">
                                        <!-- Accordion Pisos -->
                                        <div class="panel-group" id="accordion"></div>
                                    </div>
                                    <div class="col-md-1"></div>
                                </div>
                            </div>

                            <!-- Autofacturas -->
                            <div role="tabpanel" class="tab-pane" id="autofacturas">
                                <br/><br/>
                                <div class="row">
                                    <div class="col-md-1">
                                        <input inputmode="none"  type="hidden" id="vlr_emp_id" value="" />
                                    </div>
                                    <div class="col-xs-12 col-md-2"><h6><b>RUC:</b></h6></div>
                                    <div class="col-xs-12 col-md-8">
                                        <div class="form-group" id="rst_mprs_ruc"></div>
                                    </div>
                                    <div class="col-md-1"></div>
                                </div>
                                <div class="row">
                                    <div class="col-md-1"></div>
                                    <div class="col-xs-12 col-md-2"><h6><b>Empresa:</b></h6></div>
                                    <div class="col-xs-12 col-md-8">
                                        <div class="form-group" id="rst_mprs_dscrpcn"></div>
                                    </div>
                                    <div class="col-md-1"></div>
                                </div>
                                <div class="row">
                                    <div class="col-md-1"></div>
                                    <div class="col-xs-12 col-md-10">
                                        <div class="form-group" id="cnt_lst_aut_rst">
                                            <table id="lst_autorizaciones_rst" class="table table-bordered"></table>
                                        </div>
                                        <div class="form-group" id="cnt_frm_nv_aut_rst">
                                            <div class="row">
                                                <div class="col-md-12"><h5><b>Autorizaci&oacute;n Anterior (Inactivo)</b></h5></div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-3"><h6>Fecha Inicial</h6></div>
                                                <div class="col-md-3"><h6>Fecha Final</h6></div>
                                                <div class="col-md-3"><h6>Secuencia Inicial</h6></div>
                                                <div class="col-md-3"><h6>Secuencia Final</h6></div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-3"><div class="form-group"><input inputmode="none"  type="text" id="ant_aut_rst_fch_ncl" class="form-control"></div></div>
                                                <div class="col-md-3">
                                                    <div class="input-prepend input-group">
                                                        <span class="add-on input-group-addon"><i class="glyphicon glyphicon-calendar fa fa-calendar"></i></span>
                                                        <input inputmode="none"  type="text" id="ant_aut_rst_fch_fnl" class="form-control" placeholder="Fecha Fin" />
                                                    </div>
                                                </div>
                                                <div class="col-md-3"><div class="form-group"><input inputmode="none"  type="text" id="ant_aut_rst_scnc_ncl" class="form-control"></div></div>
                                                <div class="col-md-3"><div class="form-group"><input inputmode="none"  type="text" id="ant_aut_rst_scnc_fnl" onchange="fn_calcularSecuenciaInicial();" class="form-control"></div></div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12"><h5><b>Autorizaci&oacute;n Vigente (Activo)</b></h5></div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-3"><h6>Fecha Inicial</h6></div>
                                                <div class="col-md-3"><h6>Fecha Final</h6></div>
                                                <div class="col-md-3"><h6>Secuencia Inicial</h6></div>
                                                <div class="col-md-3"><h6>Secuencia Final</h6></div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <div class="input-prepend input-group">
                                                        <span class="add-on input-group-addon"><i class="glyphicon glyphicon-calendar fa fa-calendar"></i></span>
                                                        <input inputmode="none"  type="text" id="aut_rst_fch_ncl" class="form-control" placeholder="Fecha Inicial" />
                                                    </div>
                                                </div>
                                                <div class="col-md-3"><div class="form-group"><input inputmode="none"  type="text" id="aut_rst_fch_fnl" class="form-control"></div></div>
                                                <div class="col-md-3"><div class="form-group"><input inputmode="none"  type="text" id="aut_rst_scnc_ncl" class="form-control"></div></div>
                                                <div class="col-md-3"><div class="form-group"><input inputmode="none"  type="text" id="aut_rst_scnc_fnl" class="form-control"></div></div>
                                            </div>
                                            <!-- Opciones -->
                                            <div class="row">
                                                <div class="col-md-4"></div>
                                                <div class="col-md-4">
                                                    <div id="cnt_aut_rst_opc" class="form-group"></div>
                                                </div>
                                                <div class="col-md-4"></div>
                                            </div>
                                        </div>

                                        <!-- Nueva Autorizacion-->
                                        <div class="form-group" id="cnt_frm_nv_nea_aut_rst">
                                            <div class="row">
                                                <div class="col-md-12"><h5><b>Autorizaci&oacute;n</b></h5></div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-3"><h6>Fecha Inicial</h6></div>
                                                <div class="col-md-3"><h6>Fecha Final</h6></div>
                                                <div class="col-md-3"><h6>Secuencia Inicial</h6></div>
                                                <div class="col-md-3"><h6>Secuencia Final</h6></div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <div class="input-prepend input-group">
                                                        <span class="add-on input-group-addon"><i class="glyphicon glyphicon-calendar fa fa-calendar"></i></span>
                                                        <input inputmode="none"  type="text" id="nuv_aut_rst_fch_ncl" class="form-control" placeholder="Fecha Fin" />
                                                    </div>
                                                </div>
                                                <div class="col-md-3"><div class="form-group"><input inputmode="none"  type="text" id="nuv_aut_rst_fch_fnl" class="form-control" /></div></div>
                                                <div class="col-md-3"><div class="form-group"><input inputmode="none"  type="text" id="nuv_aut_rst_scnc_ncl" class="form-control" /></div></div>
                                                <div class="col-md-3"><div class="form-group"><input inputmode="none"  type="text" id="nuv_aut_rst_scnc_fnl" onchange="fn_calcularSecuenciaInicial();" class="form-control" /></div></div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-4"></div>
                                                <div class="col-md-4">
                                                    <div id="cnt_aut_nea_rst_opc" class="form-group"></div>
                                                </div>
                                                <div class="col-md-4"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-1"></div>
                                </div>
                            </div>

                            <!-- COLECCION RESTAURANTE -->
                            <div role="tabpanel" class="tab-pane" id="coleccion_rest" align="center">
                                <br/>
                                <div class="panel panel-default" style="width:1070px;" align="center">
                                    <!-- Default panel contents -->
                                    <div class="panel-heading">
                                        <div class="row">
                                            <div class="col-xs-10 col-md-9"><h6><b>RESTAURANTE COLECCI&Oacute;N DE DATOS</b></h6></div>
                                            <div class="col-md-1"></div>
                                            <div>
                                                <button type="button" class="btn btn-default" onclick="fn_nuevaColeccion();" >
                                                    <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
                                                </button>
                                                <button type="button" class="btn btn-default" onclick="fn_editColeccionRestaurante();">
                                                    <span class="glyphicon glyphicon-pencil" style="opacity: none;" aria-hidden="true"></span>
                                                </button>
                                            </div>
                                        </div>
                                        <!-- TABLA DETALLE COLECCECION RESTAURANTE -->
                                        <div align="center"
                                             style="width: 1050px; height: 300px; overflow-x: auto; overflow-y: auto;">
                                            <div class="form-group" id="_detalle_restaurante_coleccion">
                                                <table id="restaurante_coleccion" class="table table-bordered"
                                                       style="width: auto; font-size: 11px;"></table>
                                            </div>
                                        </div>

                                    </div>


                                </div>

                            </div>

                            <!-- COLECCION RESTAURANTE - IMPRESION TIPO DOCUMENTO -->
                            <div role="tabpanel" class="tab-pane" id="coleccion_documentoimpresion">
                                <br/><br/>
                                <div> 
                                    <div class="row">                                
                                        <div class="col-xs-3 text-right"><h5>Factura :</h5></div>
                                        <div class="col-xs-7">
                                            <div class="form-group">
                                                <select data-placeholder="Seleccione Tipo de Documentos" multiple="multiple" id="seleccion_factura" class="chosen-select"></select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">                                
                                        <div class="col-xs-3 text-right"><h5>Voucher :</h5></div>
                                        <div class="col-xs-7">
                                            <div class="form-group">
                                                <select data-placeholder="Seleccione Tipo de Documentos" multiple="multiple" id="seleccion_voucher" class="chosen-select"></select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">                                
                                        <div class="col-xs-3 text-right"><h5>L&iacute;nea :</h5></div>
                                        <div class="col-xs-7">
                                            <div class="form-group">
                                                <select data-placeholder="Seleccione Tipo de Documentos" multiple="multiple" id="seleccion_linea" class="chosen-select"></select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>                        
                        </div>  <!-- Fin Contenedor Tab panel -->
                    </div>
                    <!--            </div>-->

                    <!-- Botones -->
                    <div class="modal-footer">
                        <button type="button" onclick="fn_guardar();" class="btn btn-primary">Guardar</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                    </div>

                    <div id="visor" class="overlayCargando" onclick="fn_visor(0)">
                        <div id="visor_img" class=".modalCargando .modalCargandoImagen"></div>
                    </div>

                </div>
            </div>
        </div>    

        <!--Inicio de modal de coleccion NUEVA-->
        <div class="modal fade" id="mdl_nuevaColeccion" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static" aria-hidden="true" >
            <div class="modal-dialog" style="width: 700px;">
                <div class="modal-content">
                    <div class="modal-header">                  
                        <h4 class="modal-title">Colecci&oacute;n:
                            <label id="nombreColeccion"></label>
                        </h4>
                    </div>
                    <div class="modal-body">
                        <!--DETALLE Y DATOS DE COLECCION -->
                        <div class="row">                   
                            <div class="col-xs-12 col-md-12">
                                <div class="col-xs-12 col-md-6">
                                    <div class="form-group" style="height: 200px; overflow: auto;">
                                        <table id="coleccion_descripcion" class="table table-bordered" style="font-size: 11px;"></table>                                
                                    </div> 
                                </div>
                                <div class="col-xs-12 col-md-6">
                                    <div class="form-group" style="height: 200px; overflow: auto;">
                                        <table id="coleccion_datos" class="table table-bordered" style="font-size: 11px;"></table>                                
                                    </div> 
                                </div>
                            </div>
                        </div>
                        <!--TIPOS DE DATOS-->
                        <div id="tipos_de_dato"  style="height: 180px; overflow: auto;">
                            <div class="row"> 
                                <div class="col-xs-6 col-md-4">
                                    <div class="btn-group">
                                        <h5 class="text-right">Especifica Valor: <input inputmode="none"  disabled="disabled" type="checkbox" value="1" id="check_especifica"/></h5>
                                    </div>
                                </div>        
                                <div class="col-xs-6 col-md-4">
                                    <div class="btn-group">
                                        <h5 class="text-right">Obligatorio: <input inputmode="none"  disabled="disabled" type="checkbox" value="1" id="check_obligatorio"/></h5>
                                    </div>
                                </div>       
                                <div class="col-xs-6 col-md-4">
                                    <div class="btn-group">
                                        <h5 class="text-right">Tipo de dato: <label value="1" id="lbl_tipoDato"></label></h5>
                                    </div>
                                </div>       

                            </div>
                            <div class="row">
                                <div class="col-xs-3 text-right"><h5>Varchar:</h5></div>
                                <div class="col-xs-7">
                                    <div class="form-group">
                                        <input inputmode="none"  maxlength="50" type="text" class="form-control" id="tipo_varchar" style="text-transform:uppercase;" />
                                    </div>
                                </div>
                            </div> 
                            <div class="row">
                                <div class="col-xs-3 text-right"><h5>Entero:</h5></div>
                                <div class="col-xs-4">
                                    <div class="form-group">
                                        <input inputmode="none"  maxlength="50" type="text" class="form-control" id="tipo_entero" />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-3 text-right"><h5>Fecha:</h5></div>
                                <div class="col-xs-4">
                                    <div class="form-group">
                                        <div class="input-prepend input-group">
                                            <span class="add-on input-group-addon"><i class="glyphicon glyphicon-calendar fa fa-calendar"></i></span>
                                            <input inputmode="none"  type="text" value="" class="form-control" name="tipo_fecha" id="tipo_fecha" placeholder="Fecha" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">                     	
                                <div class="col-xs-3 text-right"><h5>Seleci&oacute;n:</h5></div>
                                <div class="col-xs-3">
                                    <div class="form-group">
                                        <input inputmode="none"  type="checkbox" id="tipo_bit" data-off-text="No" data-on-text="Si" />         
                                    </div>
                                </div>                             
                            </div>
                            <div class="row">
                                <div class="col-xs-3 text-right"><h5>Num&eacute;rico:</h5></div>
                                <div class="col-xs-4">
                                    <div class="form-group">
                                        <input inputmode="none"  maxlength="50" type="text" class="form-control" id="tipo_numerico" />
                                    </div>
                                </div>
                            </div>                    
                            <div class="row">                      
                                <div class="col-md-3"><h5 class="text-right">Rango Fecha:</h5></div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="FechaInicial" class="control-label">Fecha Inicio</label>
                                        <div class="input-prepend input-group">
                                            <span class="add-on input-group-addon"><i class="glyphicon glyphicon-calendar fa fa-calendar"></i></span>
                                            <input inputmode="none"  type="text" value="" class="form-control" name="FechaInicial" id="FechaInicial" placeholder="Fecha Inicio" />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="FechaFinal" class="control-label">Fecha Fin</label>
                                        <div class="input-prepend input-group">
                                            <span class="add-on input-group-addon"><i class="glyphicon glyphicon-calendar fa fa-calendar"></i></span>
                                            <input inputmode="none"  type="text" value="" class="form-control" name="FechaFinal" id="FechaFinal" placeholder="Fecha Fin" />
                                        </div>
                                    </div>
                                </div>                      
                            </div> 

                            <div class="row">                      
                                <div class="col-md-3"><h5 class="text-right">Rango Decimal:</h5></div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="Min" class="control-label">Minimo</label>                                
                                        <div class="form-group">
                                            <input inputmode="none"  maxlength="50" type="text" class="form-control" id="rango_minimo" />
                                        </div>                                
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="Max" class="control-label">M&aacute;ximo.</label>                                
                                        <div class="form-group">
                                            <input inputmode="none"  maxlength="50" type="text" class="form-control" id="rango_maximo" />
                                        </div>                                
                                    </div>
                                </div>                      
                            </div>                     
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal" onclick="fn_verModal();">Cancelar</button>
                        <button type="button" class="btn btn-primary" onclick="fn_guardarRestauranteColeccion();">Guardar</button>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
        <!--fin-->

        <!--Inicio de modal de coleccion MODIFICAR -->
        <div class="modal fade" id="mdl_editColeccion" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static">
            <div class="modal-dialog" style="width: 700px;" >
                <div class="modal-content">
                    <div class="modal-header">                  
                        <h4 class="modal-title">Colecci&oacute;n:
                            <label id="edit_nombreColeccion"></label>
                        </h4>
                    </div>
                    <div class="modal-body">  
                        <div class="col-xs-12 text-right"><h6>Est&aacute; Activo?: <input inputmode="none"  type="checkbox" checked="checked" id="check_estado"/></h6></div> 
                        <!--TIPOS DE DATOS-->
                        <div id="tipos_de_dato">
                            <div class="row"> 
                                <div class="col-xs-6 col-md-4">
                                    <div class="btn-group">
                                        <h5 class="text-right">Especifica Valor: <input inputmode="none"  disabled="disabled" type="checkbox" id="edit_check_especifica"/></h5>
                                    </div>
                                </div>        
                                <div class="col-xs-6 col-md-4">
                                    <div class="btn-group">
                                        <h5 class="text-right">Obligatorio: <input inputmode="none"  disabled="disabled" type="checkbox" id="edit_check_obligatorio"/></h5>
                                    </div>
                                </div>       
                                <div class="col-xs-6 col-md-4">
                                    <div class="btn-group">
                                        <h5 class="text-right">Tipo de dato: <label value="1" id="edit_lbl_tipoDato"></label></h5>
                                    </div>
                                </div>       

                            </div>
                            <div class="row">
                                <div class="col-xs-3 text-right"><h5>Varchar:</h5></div>
                                <div class="col-xs-7">
                                    <div class="form-group">
                                        <input inputmode="none"  type="text" class="form-control" id="tipo_varchar_edit" style="text-transform:uppercase;"/>
                                    </div>
                                </div>
                            </div> 
                            <div class="row">
                                <div class="col-xs-3 text-right"><h5>Entero:</h5></div>
                                <div class="col-xs-4">
                                    <div class="form-group">
                                        <input inputmode="none"  maxlength="50" type="text" class="form-control" id="tipo_entero_edit" />
                                    </div>
                                </div>
                            </div> 

                            <div class="row">
                                <div class="col-xs-3 text-right"><h5>Fecha:</h5></div>
                                <div class="col-xs-4">
                                    <div class="form-group">
                                        <div class="input-prepend input-group">
                                            <span class="add-on input-group-addon"><i class="glyphicon glyphicon-calendar fa fa-calendar"></i></span>
                                            <input inputmode="none"  type="text" value="" class="form-control" name="tipo_fecha_edit" id="tipo_fecha_edit" placeholder="Fecha" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">                     	
                                <div class="col-xs-3 text-right"><h5>Selecci&oacute;n:</h5></div>
                                <div class="col-xs-3">
                                    <div class="form-group">
                                        <input inputmode="none"  type="checkbox" id="tipo_bit_edit" data-off-text="No" data-on-text="Si" />         
                                    </div>
                                </div>                             
                            </div>
                            <div class="row">
                                <div class="col-xs-3 text-right"><h5>Num&eacute;rico:</h5></div>
                                <div class="col-xs-4">
                                    <div class="form-group">
                                        <input inputmode="none"  maxlength="50" type="text" class="form-control" id="tipo_numerico_edit" />
                                    </div>
                                </div>
                            </div>                   
                            <div class="row">                      
                                <div class="col-xs-3 text-right"><h5 class="text-right">Rango Fecha:</h5></div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="FechaInicial" class="control-label">Fecha Inicio</label>
                                        <div class="input-prepend input-group">
                                            <span class="add-on input-group-addon"><i class="glyphicon glyphicon-calendar fa fa-calendar"></i></span>
                                            <input inputmode="none"  type="text" value="" class="form-control" name="FechaInicial_edit" id="FechaInicial_edit" placeholder="Fecha Inicio" />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="FechaFinal" class="control-label">Fecha Fin</label>
                                        <div class="input-prepend input-group">
                                            <span class="add-on input-group-addon"><i class="glyphicon glyphicon-calendar fa fa-calendar"></i></span>
                                            <input inputmode="none"  type="text" value="" class="form-control" name="FechaFinal_edit" id="FechaFinal_edit" placeholder="Fecha Fin" />
                                        </div>
                                    </div>
                                </div>                      
                            </div>                     
                            <div class="row">                      
                                <div class="col-md-3"><h5 class="text-right">Rango Decimal:</h5></div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="Min" class="control-label">Minimo</label>                                
                                        <div class="form-group">
                                            <input inputmode="none"  maxlength="50" type="text" class="form-control" id="rango_minimo_edit" />
                                        </div>                                
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="Max" class="control-label">M&aacute;ximo.</label>                                
                                        <div class="form-group">
                                            <input inputmode="none"  maxlength="50" type="text" class="form-control" id="rango_maximo_edit" />
                                        </div>                                
                                    </div>
                                </div>                      
                            </div>                     
                        </div> 
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal" onclick="fn_verModal();">Cancelar</button>
                        <button type="button" class="btn btn-primary" onclick="fn_modificaRestauranteColeccion();">Guardar</button>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
        <!--fin-->

        <input inputmode="none"  id="slccn_rst_id" type="hidden" value=""/>
        <input inputmode="none"  id="emp_confirmacion_ok" type="hidden" value=""/>
        <input inputmode="none"  id="IDRestaurante" type="hidden" />

        <!-- JavaScript -->
        <script type="text/javascript" src="../../js/jquery1.11.1.js"></script>
        <script type="text/javascript" src="../../js/ajax_datatables.js"></script>
        <script type="text/javascript" src="../../bootstrap/js/bootstrap.js"></script>
        <script type="text/javascript" src="../../bootstrap/js/moment.js"></script>
        <script type="text/javascript" src="../../bootstrap/js/daterangepicker.js"></script>     
        <script type="text/javascript" src="../../bootstrap/js/bootstrap-dataTables.js"></script>
        <script type="text/javascript" src="../../bootstrap/js/bootstrap-editable.min.js"></script>
        <script type="text/javascript" src="../../bootstrap/js/switch.js"></script>
        <script language="javascript1.1" type="text/javascript" src="../../js/chosen.jquery.min.js"></script>
        <script language="javascript1.1" type="text/javascript" src="../../js/chosen.proto.min.js"></script>
        <script language="javascript1.1" src="../../js/alertify.js"></script>
        <script type="text/javascript" src="../../bootstrap/js/inputnumber.js"></script>
        <script type="text/javascript" src="../../js/ajax_adminrestaurante.js"></script>
        <script src="../../bootstrap/js/switch.js"></script>  

        <script>indice = 1;</script>
    </body>
</html>