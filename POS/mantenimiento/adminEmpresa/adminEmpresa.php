<?php
/* * *******************************************************
 *          DESARROLLADO POR: Alex Merino                *
 *          DESCRIPCION: PHP vista -(Empresa) php        *
 *          FECHA CREACION: 14/04/2018                   *
 * ******************************************************* */

session_start();
include_once("../../seguridades/seguridad.inc");

if (!isset($_SESSION['validado'])) {
    include_once('../../seguridades/Adm_seguridad.inc');
} else {
    include_once('../../system/conexion/clase_sql.php');
    include_once('../../clases/clase_seguridades.php');
    include_once('../../clases/clase_adminEmpresa.php');
    $empresa = new empresa();
    ?>
    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <!-- LIBRERIAS -->
        <!---------------------------------------------------
        ESTILOS
----------------------------------------------------->
        <link rel="stylesheet" href="../../css/jquery-ui.css" type="text/css"/>
        <!--<link rel="stylesheet" href="../../css/est_pantallas.css" type="text/css"/>-->
        <link rel="stylesheet" type="text/css" href="../../bootstrap/css/bootstrap.css "/>
        <link rel="stylesheet" type="text/css" href="../../bootstrap/templete/css/bootstrap.css "/>
        <link rel="stylesheet" type="text/css" media="all" href="../../bootstrap/css/daterangepicker-bs3.css"/>
        <link rel="stylesheet" type="text/css" href="../../bootstrap/css/switch.css"/>
        <link rel="stylesheet" type="text/css" href="../../css/alertify.core.css"/>
        <link rel="stylesheet" type="text/css" href="../../css/alertify.default.css"/>
        <link rel="stylesheet" type="text/css" href="../../css/est_administracionPantalla.css"/>
        <link rel="stylesheet" type="text/css" href="../../bootstrap/css/bootstrap.css"/>
        <!--<link rel="stylesheet" type="text/css" href="../../bootstrap/css/bootstrap-toggle.min.css" />-->
        <link rel="stylesheet" type="text/css" href="../../css/chosen.css"/>
        <title>Administraci&oacute;n - Empresa</title>
    </head>
    <body>
    <input inputmode="none"  id="idUser" type="hidden" value="<?php echo $_SESSION['usuarioId']; ?>"/>
    <input inputmode="none"  id="cadenas" type="hidden" value="<?php echo $_SESSION['cadenaId']; ?>"/>
    <input inputmode="none"  id="restaurante" type="hidden" value="<?php echo $_SESSION['rstId']; ?>"/>


    <div class="superior">
        <div class="menu" style="width: 400px; align: center; margin-top: 5px;">
            <!--<ul>
                <li>
                    <input inputmode="none"  id="btn_agregar" type="button" onclick="fn_accionar('CallWS')" class="botonhabilitado" value="Agregar"/>
                    <!--<input inputmode="none"  style="margin-top: 7px;" id="btn_agregar" type="button" onclick="fn_agregar()" class="botonhabilitado" value="Agregar"/>-->
            <!--</li>-->
            <!-- <li><input inputmode="none"  id="btn_cancelar" type="button" onclick="fn_borrar()" class="botonhabilitado" value="Cancelar"/></li>-->
            <!-- <li><input inputmode="none"  id="btn_agregar" type="button" onclick="fn_agregarcate()" class="botonhabilitado" value="Agregar"/></li>  -->
            <!--</ul>-->
        </div>
        <div class="tituloPantalla">
            <h1>EMPRESA</h1>
        </div>
    </div>
    <div class="contenedor">
        <div class="inferior">

            <br/>
            <br/>

            <div class="contenedor">
                <div class="inferior" align="center">
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
                    <div id="tabla_empresa">
                        <table class="table table-bordered table-hover" id="detalle_empresa" border="1" cellpadding="1"
                               cellspacing="0">
                        </table>
                    </div>
                </div>
            </div>
            <div id="load">
            </div>
            <!-- INICIO MODAL PARA MODIFICAR EMPRESA -->
            <div class="modal fade" id="ModalModificar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
                 aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog modal-lg" style="width:1100px;">
                    <div class="modal-content">
                        <div class="modal-header panel-footer">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                        aria-hidden="true">&times;</span></button>
                            <b>
                                <h4 class="modal-title" id="titulomodalModificar"/>
                            </b>
                        </div>
                        <ul id="myTabs" class="nav nav-tabs" role="tablist">
                            <li role="presentation" class="active"><a href="#empresa" aria-controls="empresa" role="tab"
                                                                      data-toggle="tab">
                                    <span class="glyphicon glyphicon-home" style="font-size: 20px;"></span>
                                    &nbsp;Información empresa
                                </a></li>
                            <li role="presentation"><a href="#fiscal" aria-controls="fiscal" role="tab"
                                                       data-toggle="tab">
                                    <span class="glyphicon glyphicon-edit" style="font-size: 20px;"></span>
                                    &nbsp;Información fiscal
                                </a></li>
                            <li role="presentation"><a href="#politicasempresa" aria-controls="politicas" role="tab"
                                                       data-toggle="tab">
                                    <span class="glyphicon glyphicon-cog" style="font-size: 20px;"></span>
                                    &nbsp;Pol&iacute;ticas de Configuraci&oacute;n
                                </a></li>

                        </ul>
                        <br/>
                        <div align="right" class="col-xs-12 col-x"><b>Est&aacute; Activo?:
                                <input inputmode="none"  type="checkbox" id="opcion_Modificar"/> </b>
                        </div>
                        <div class="modal-body">
                            <div role="tabpanel">
                                <div id="pst_cnt" class="tab-content">
                                    <div class="tab-content">
                                        <div role="tabpanel" class="tab-pane active" id="empresa">
                                            <div class="row" style="display: none">
                                                <div class="col-xs-1"></div>
                                                <div class="col-xs-3">
                                                    <h5>Id Empresa:</h5>
                                                </div>
                                                <div class="col-xs-7">
                                                    <div class="form-group" class="col-xs-1">
                                                        <input inputmode="none"  type="text" style="text-align:left" class="form-control"
                                                               id="idEmpresa" readonly/>
                                                    </div>
                                                </div>
                                                <div class="col-xs-1"></div>
                                            </div>
                                            <div class="row">
                                                <div class="col-xs-1"></div>
                                                <div class="col-xs-3">
                                                    <h5 id="textTitulo"></h5>
                                                </div>
                                                <div class="col-xs-7">
                                                    <div class="form-group" class="col-xs-1">
                                                        <input inputmode="none"  type="text" style="text-align:left" class="form-control"
                                                               id="txtruc" disabled/>
                                                    </div>
                                                </div>
                                                <div class="col-xs-1"></div>
                                            </div>
                                            <div class="row">
                                                <div class="col-xs-1"></div>
                                                <div class="col-xs-3">
                                                    <h5>Nombre:</h5>
                                                </div>
                                                <div class="col-xs-7">
                                                    <div class="form-group" class="col-xs-1">
                                                        <input inputmode="none"  type="text" onkeydown="return soloLetras(event)"
                                                               style="text-align:left"
                                                               class="form-control" id="txtnombre" disabled/>
                                                    </div>
                                                </div>
                                                <div class="col-xs-1"></div>
                                            </div>
                                            <div class="row">
                                                <div class="col-xs-1"></div>
                                                <div class="col-xs-3">
                                                    <h5>Teléfono:</h5>
                                                </div>
                                                <div class="col-xs-7">
                                                    <div class="form-group" class="col-xs-1">
                                                        <input inputmode="none"  type="text" style="text-align:left"
                                                               onkeydown="return validarNumeros(event)"
                                                               class="form-control" id="txttelefono" disabled/>
                                                    </div>
                                                </div>
                                                <div class="col-xs-1"></div>
                                            </div>
                                            <div class="row">
                                                <div class="col-xs-1"></div>
                                                <div class="col-xs-3">
                                                    <h5>Ciudad:</h5>
                                                </div>
                                                <div class="col-xs-7">
                                                    <div class="form-group" class="col-xs-1">
                                                        <input inputmode="none"  type="text" onkeydown="return soloLetras(event)"
                                                               style="text-align:left"
                                                               class="form-control" id="txtciudad" readonly/>
                                                    </div>
                                                </div>
                                                <div class="col-xs-1"></div>
                                            </div>
                                            <div class="row">
                                                <div class="col-xs-1"></div>
                                                <div class="col-xs-3">
                                                    <h5>Dirección:</h5>
                                                </div>
                                                <div class="col-xs-7">
                                                    <div class="form-group" class="col-xs-1">
                                                        <input inputmode="none"  type="text" style="text-align:left" class="form-control"
                                                               id="txtdireccion" readonly/>
                                                    </div>
                                                </div>
                                                <div class="col-xs-1"></div>
                                            </div>
                                        </div>
                                        <div role="tabpanel" class="tab-pane" id="fiscal">
                                            <div class="row">
                                                <div class="col-xs-1"></div>
                                                <div class="col-xs-3">
                                                    <h5>Razón Social:</h5>
                                                </div>
                                                <div class="col-xs-7">
                                                    <div class="form-group" class="col-xs-1">
                                                        <input inputmode="none"  type="text" style="text-align:left" class="form-control"
                                                               id="txtrazonSocial" onpaste="return false"/>
                                                    </div>
                                                </div>
                                                <div class="col-xs-1"></div>
                                            </div>
                                            <div class="row">
                                                <div class="col-xs-1"></div>
                                                <div class="col-xs-3">
                                                    <h5>Tipo contribuyente:</h5>
                                                </div>
                                                <div class="col-xs-7">
                                                    <div class="form-group" class="col-xs-1">
                                                        <input inputmode="none"  type="text" style="text-align:left"
                                                               onkeydown="return soloLetras(event)"
                                                               onpaste="return false" class="form-control"
                                                               id="txttipoContibuyente"/>
                                                    </div>
                                                </div>
                                                <div class="col-xs-1"></div>
                                            </div>
                                            <div class="row">
                                                <div class="col-xs-1"></div>
                                                <div class="col-xs-3">
                                                    <h5>Resolución:</h5>
                                                </div>
                                                <div class="col-xs-7">
                                                    <div class="form-group" class="col-xs-1">
                                                        <input inputmode="none"  type="text" style="text-align:left"
                                                               onkeydown="return validarNumeros(event)"
                                                               onpaste="return false" class="form-control"
                                                               id="txtresolucion"/>
                                                    </div>
                                                </div>
                                                <div class="col-xs-1"></div>
                                            </div>
                                            <div class="row">
                                                <div class="col-xs-1"></div>
                                                <div class="col-xs-3">
                                                    <h5>Fecha resolución:</h5>
                                                </div>
                                                <div class="col-xs-7">
                                                    <div class="input-prepend input-group">
                                                        <span class="add-on input-group-addon"><i
                                                                    class="glyphicon glyphicon-calendar fa fa-calendar"></i></span>
                                                        <input inputmode="none"  type="text" style="text-align:left" class="form-control"
                                                               id="txtfechRes"/>
                                                    </div>
                                                </div>
                                                <div class="col-xs-1"></div>
                                            </div>
                                            <br/>
                                            <div class="row">
                                                <div class="col-xs-1"></div>
                                                <div class="col-xs-3">
                                                    <h5>Tipo de emision :</h5>
                                                </div>
                                                <div class="col-xs-7">
                                                    <div id="tipEmision"></div>
                                                </div>
                                                <div class="col-xs-1"></div>
                                            </div>
                                            <br/>
                                            <div class="row">
                                                <div class="col-xs-1"></div>
                                                <div class="col-xs-3">
                                                    <h5>Tipo de ambiente:</h5>
                                                </div>
                                                <div class="col-xs-7">
                                                    <div id="tipAmbiente"></div>
                                                </div>
                                                <div class="col-xs-1"></div>
                                            </div>
                                            <br/>
                                            <div class="row" id="divContabilidad">
                                                <div class="col-xs-1"></div>
                                                <div class="col-xs-3">
                                                    <h5>Llevar contabilidad:</h5>
                                                </div>
                                                <div class="col-xs-7">
                                                    <input inputmode="none"  type="checkbox" id="txtcontabilidad" data-off-text="No"
                                                           data-on-text="Si"/>
                                                </div>
                                                <div class="col-xs-1"></div>
                                            </div>
                                        </div>
                                        <div role="tabpanel" class="tab-pane" id="politicasempresa" align="center">
                                            <br/>
                                            <div class="panel panel-default" style="width:1070px;" align="center">
                                                <!-- Default panel contents -->
                                                <div class="panel-heading">
                                                    <div class="row">
                                                        <div class="col-xs-10 col-md-9"><h6><b>EMPRESA COLECCI&Oacute;N
                                                                    DE DATOS</b></h6></div>
                                                        <div class="col-md-1"></div>
                                                        <div>
                                                            <button type="button" class="btn btn-default"
                                                                    onclick="fn_nuevaEmpresaColeccion();">
                                                                <span class="glyphicon glyphicon-plus"
                                                                      aria-hidden="true"></span>
                                                            </button>
                                                            <button type="button" class="btn btn-default"
                                                                    onclick="fn_editColeccionEmpresa();">
                                                                <span class="glyphicon glyphicon-pencil"
                                                                      style="opacity: inherit"
                                                                      aria-hidden="true"></span>
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <!-- TABLA DETALLE COLECCECION RESTAURANTE -->
                                                    <div align="center"
                                                         style="width: 1050px; height: 300px; overflow-x: auto; overflow-y: auto;">
                                                        <div class="form-group" id="_detalle_empresa_coleccion">
                                                            <table id="empresa_coleccion" class="table table-bordered"
                                                                   style="width: auto; font-size: 11px;"></table>
                                                        </div>
                                                    </div>

                                                </div>


                                            </div>

                                        </div>


                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary" onclick="fn_guardarCambios(2)">Aceptar
                            </button>
                            <button type="button" class="btn btn-default" onclick="" data-dismiss="modal">Cancelar
                            </button>
                        </div>

                    </div>
                </div>
            </div>
            <!--FIN MODAL MODIFICAR EMPRESA-->
        </div>
    </div>
    <div class="modal fade" id="mdl_nuevaEColeccion" tabindex="-1" role="dialog" data-keyboard="false"
         data-backdrop="static" aria-hidden="true">
        <div class="modal-dialog" style="width: 700px;">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Colecci&oacute;n:
                        <label id="nombreColeccionE"></label>
                    </h4>
                </div>
                <div class="modal-body">
                    <!--DETALLE Y DATOS DE COLECCION -->
                    <div class="row">
                        <div class="col-xs-12 col-md-12">
                            <div class="col-xs-12 col-md-6">
                                <div class="form-group" style="height: 200px; overflow: auto;">
                                    <table id="coleccion_descripcionE" class="table table-bordered"
                                           style="font-size: 11px;"></table>
                                </div>
                            </div>
                            <div class="col-xs-12 col-md-6">
                                <div class="form-group" style="height: 200px; overflow: auto;">
                                    <table id="coleccion_datosE" class="table table-bordered"
                                           style="font-size: 11px;"></table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--TIPOS DE DATOS-->
                    <div id="tipos_de_datoE"
                    ">
                    <div class="row">
                        <div class="col-xs-6 col-md-4">
                            <div class="btn-group">
                                <h5 class="text-right">Especifica Valor: <input inputmode="none"  disabled="disabled" type="checkbox"
                                                                                value="1" id="check_especificaE"/></h5>
                            </div>
                        </div>
                        <div class="col-xs-6 col-md-4">
                            <div class="btn-group">
                                <h5 class="text-right">Obligatorio: <input inputmode="none"  disabled="disabled" type="checkbox" value="1"
                                                                           id="check_obligatorioE"/></h5>
                            </div>
                        </div>
                        <div class="col-xs-6 col-md-4">
                            <div class="btn-group">
                                <h5 class="text-right">Tipo de dato: <label value="1" id="lbl_tipoDatoE"></label></h5>
                            </div>
                        </div>

                    </div>
                    <div class="row">
                        <div class="col-xs-3 text-right"><h5>Varchar:</h5></div>
                        <div class="col-xs-7">
                            <div class="form-group">
                                <input inputmode="none"  maxlength="50" type="text" class="form-control" id="tipo_varcharE"
                                       style="text-transform:uppercase;"/>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-3 text-right"><h5>Entero:</h5></div>
                        <div class="col-xs-4">
                            <div class="form-group">
                                <input inputmode="none"  maxlength="50" type="text" class="form-control" id="tipo_enteroE"/>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-3 text-right"><h5>Fecha:</h5></div>
                        <div class="col-xs-4">
                            <div class="form-group">
                                <div class="input-prepend input-group">
                                    <span class="add-on input-group-addon"><i
                                                class="glyphicon glyphicon-calendar fa fa-calendar"></i></span>
                                    <input inputmode="none"  type="text" value="" class="form-control" name="tipo_fecha" id="tipo_fechaE"
                                           placeholder="Fecha"/>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-3 text-right"><h5>Seleci&oacute;n:</h5></div>
                        <div class="col-xs-3">
                            <div class="form-group">
                                <input inputmode="none"  type="checkbox" id="tipo_bitE" data-off-text="No" data-on-text="Si"/>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-3 text-right"><h5>Num&eacute;rico:</h5></div>
                        <div class="col-xs-4">
                            <div class="form-group">
                                <input inputmode="none"  maxlength="50" type="text" class="form-control" id="tipo_numericoE"/>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3"><h5 class="text-right">Rango Fecha:</h5></div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="FechaInicial" class="control-label">Fecha Inicio</label>
                                <div class="input-prepend input-group">
                                    <span class="add-on input-group-addon"><i
                                                class="glyphicon glyphicon-calendar fa fa-calendar"></i></span>
                                    <input inputmode="none"  type="text" value="" class="form-control" name="FechaInicial"
                                           id="FechaInicialE" placeholder="Fecha Inicio"/>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="FechaFinal" class="control-label">Fecha Fin</label>
                                <div class="input-prepend input-group">
                                    <span class="add-on input-group-addon"><i
                                                class="glyphicon glyphicon-calendar fa fa-calendar"></i></span>
                                    <input inputmode="none"  type="text" value="" class="form-control" name="FechaFinal" id="FechaFinalE"
                                           placeholder="Fecha Fin"/>
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
                                    <input inputmode="none"  maxlength="50" type="text" class="form-control" id="rango_minimoE"/>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="Max" class="control-label">M&aacute;ximo.</label>
                                <div class="form-group">
                                    <input inputmode="none"  maxlength="50" type="text" class="form-control" id="rango_maximoE"/>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal" onclick="fn_verModalE();">Cancelar
                </button>
                <button type="button" class="btn btn-primary" onclick="fn_insertarEColeccion();">Guardar</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

    <div class="modal fade" id="mdl_editEColeccion" tabindex="-1" role="dialog" data-keyboard="false"
         data-backdrop="static">
        <div class="modal-dialog" style="width: 700px;">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Colecci&oacute;n:
                        <label id="edit_nombreColeccionE"></label>
                    </h4>
                </div>
                <div class="modal-body">
                    <div class="col-xs-12 text-right"><h6>Est&aacute; Activo?: <input inputmode="none"  type="checkbox" checked="checked"
                                                                                      id="check_estadoE"/></h6></div>
                    <!--TIPOS DE DATOS-->
                    <div id="tipos_de_dato">
                        <div class="row">
                            <div class="col-xs-6 col-md-4">
                                <div class="btn-group">
                                    <h5 class="text-right">Especifica Valor: <input inputmode="none"  disabled="disabled" type="checkbox"
                                                                                    id="edit_check_especificaE"/></h5>
                                </div>
                            </div>
                            <div class="col-xs-6 col-md-4">
                                <div class="btn-group">
                                    <h5 class="text-right">Obligatorio: <input inputmode="none"  disabled="disabled" type="checkbox"
                                                                               id="edit_check_obligatorioE"/></h5>
                                </div>
                            </div>
                            <div class="col-xs-6 col-md-4">
                                <div class="btn-group">
                                    <h5 class="text-right">Tipo de dato: <label value="1"
                                                                                id="edit_lbl_tipoDatoE"></label></h5>
                                </div>
                            </div>

                        </div>
                        <div class="row">
                            <div class="col-xs-3 text-right"><h5>Varchar:</h5></div>
                            <div class="col-xs-7">
                                <div class="form-group">
                                    <input inputmode="none"  type="text" class="form-control" id="tipo_varchar_editE"
                                           style="text-transform:uppercase;"/>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-3 text-right"><h5>Entero:</h5></div>
                            <div class="col-xs-4">
                                <div class="form-group">
                                    <input inputmode="none"  maxlength="50" type="text" class="form-control" id="tipo_entero_editE"/>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xs-3 text-right"><h5>Fecha:</h5></div>
                            <div class="col-xs-4">
                                <div class="form-group">
                                    <div class="input-prepend input-group">
                                        <span class="add-on input-group-addon"><i
                                                    class="glyphicon glyphicon-calendar fa fa-calendar"></i></span>
                                        <input inputmode="none"  type="text" value="" class="form-control" name="tipo_fecha_edit"
                                               id="tipo_fecha_editE" placeholder="Fecha"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-3 text-right"><h5>Selecci&oacute;n:</h5></div>
                            <div class="col-xs-3">
                                <div class="form-group">
                                    <input inputmode="none"  type="checkbox" id="tipo_bit_editE" data-off-text="No" data-on-text="Si"/>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-3 text-right"><h5>Num&eacute;rico:</h5></div>
                            <div class="col-xs-4">
                                <div class="form-group">
                                    <input inputmode="none"  maxlength="50" type="text" class="form-control" id="tipo_numerico_editE"/>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-3 text-right"><h5 class="text-right">Rango Fecha:</h5></div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="FechaInicial" class="control-label">Fecha Inicio</label>
                                    <div class="input-prepend input-group">
                                        <span class="add-on input-group-addon"><i
                                                    class="glyphicon glyphicon-calendar fa fa-calendar"></i></span>
                                        <input inputmode="none"  type="text" value="" class="form-control" name="FechaInicial_edit"
                                               id="FechaInicial_editE" placeholder="Fecha Inicio"/>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="FechaFinal" class="control-label">Fecha Fin</label>
                                    <div class="input-prepend input-group">
                                        <span class="add-on input-group-addon"><i
                                                    class="glyphicon glyphicon-calendar fa fa-calendar"></i></span>
                                        <input inputmode="none"  type="text" value="" class="form-control" name="FechaFinal_edit"
                                               id="FechaFinal_editE" placeholder="Fecha Fin"/>
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
                                        <input inputmode="none"  maxlength="50" type="text" class="form-control" id="rango_minimo_editE"/>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="Max" class="control-label">M&aacute;ximo.</label>
                                    <div class="form-group">
                                        <input inputmode="none"  maxlength="50" type="text" class="form-control" id="rango_maximo_editE"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal1" onclick="fn_verModalE();">
                        Cancelar
                    </button>
                    <button type="button" class="btn btn-primary" onclick="fn_modificarEmpresaColeccion();">Guardar
                    </button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
    <!--fin-->

    <!-- VARIABLES OCULTAS PARA UTILIZAR -->
    <input inputmode="none"  type="hidden" id="idPais"/>
    <input inputmode="none"  type="hidden" id="namePais"/>
    <input inputmode="none"  type="hidden" id="IDEmpresa"/>

    <!-- LIBRERIAS Y CSS -->

    <script src="../../js/jquery1.11.1.js"></script>
    <script type="text/javascript" src="../../js/jquery-ui.js"></script>
    <script language="javascript1.1" src="../../js/alertify.js"></script>


    <script language="javascript1.1" type="text/javascript" src="../../js/idioma.js"></script>
    <script language="javascript1.1" type="text/javascript" src="../../js/js_validaciones.js"></script>
    <script language="javascript1.1" type="text/javascript" src="../../js/ajax_datatables.js"></script>

    <script language="javascript1.1" type="text/javascript" src="../../bootstrap/js/bootstrap.js"></script>
    <script language="javascript1.1" type="text/javascript" src="../../bootstrap/js/bootstrap-tooltip.js"></script>
    <script language="javascript1.1" type="text/javascript" src="../../bootstrap/js/bootstrap-dataTables.js"></script>
    <!--<script language="javascript1.1" type="text/javascript" src="../../bootstrap/js/bootstrap-toggle.min.js"></script>-->
    <script type="text/javascript" src="../../bootstrap/js/inputnumber.js"></script>
    <script type="text/javascript" src="../../bootstrap/js/moment.js"></script>
    <script type="text/javascript" src="../../bootstrap/js/daterangepicker.js"></script>
    <script type="text/javascript" src="../../bootstrap/js/switch.js"></script>
    <script language="javascript1.1" type="text/javascript" src="../../js/chosen.jquery.js"></script>
    <script language="javascript1.1" type="text/javascript" src="../../js/chosen.jquery.min.js"></script>
    <script language="javascript1.1" type="text/javascript" src="../../js/chosen.proto.js"></script>
    <script language="javascript1.1" type="text/javascript" src="../../js/chosen.proto.min.js"></script>
    <script language="javascript1.1" type="text/javascript" src="../../js/chosen.order.jquery.min.js"></script>
    <script type="text/javascript" src="../../js/ajax_adminEmpresa.js"></script>
    </body>
    </html>
<?php } ?>


