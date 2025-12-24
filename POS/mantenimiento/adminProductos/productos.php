<?php
session_start();
include_once '../../seguridades/seguridad.inc';
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta charset="UTF-8"/>
        <title>Administración Plus</title>
        <link rel="stylesheet" type="text/css" href="../../bootstrap/css/bootstrap.css"/>
        <link rel="stylesheet" type="text/css" href="../../bootstrap/css/daterangepicker-bs3.css" />
        <link rel="stylesheet" type="text/css" href="../../bootstrap/css/switch.css" />
        <link rel="stylesheet" type="text/css" href="../../css/alertify.core.css"/>
        <link rel="stylesheet" type="text/css" href="../../css/alertify.default.css"/>
        <link rel="stylesheet" type="text/css" href="../../bootstrap/css/switch.css"/>
        <link rel="stylesheet" type="text/css" href="../../css/chosen.css" />
        <link rel="stylesheet" type="text/css" href="../../css/jquery-ui.css"/>
        <link rel="stylesheet" type="text/css" href="../../css/jquery-ui-sortable.css"/>
        <link rel="stylesheet" type="text/css" href="../../css/productos.css"/>
        <link rel="stylesheet" type="text/css" href="../../css/textArea.css" />
    </head>
    <body>
        <div class="superior">
            <div class="menu" align="center" style="width: 300px;">
                <ul>
                    <li>
                        <button id="agregar" onclick="agregarNuevoProducto()" class="botonMnSpr l-basic-elaboration-document-plus">
                            <span>Nuevo</span>
                        </button>
                    </li>
                </ul>
            </div>
            <div class="tituloPantalla">
                <h1>PRODUCTOS</h1>
            </div>
        </div>

        <div class="inferior">
            <!-- Tabla Plus -->
            <div id="contenedor_plus" class="panel panel-default">                
                <div class="panel-heading">
                    <div class="row">
                        <div class="col-sm-8">
                            <div id="filtroClasificacion" class="btn-group" data-toggle="buttons"></div>
                            <!--</div>-->
                        </div>
                    </div>
                </div>
                <div id="producto_plu" class="panel-body">
                    <table id="plus" class="table table-bordered table-hover"></table>
                </div>
            </div> 
        </div> 

        <div class="modal fade" id="mdlProducto" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
            <div class="modal-dialog" style="width:1100px; margin: 5px auto;">
                <div class="modal-content">
                    <div class="modal-header active panel-footer">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title active" id="titulo1"></h4>
                    </div>
                    <div class="contenedor_tabs">
                        <br/>
                        <ul id="cntPstModal" class="nav nav-tabs">
                            <li id="pstConfigBoton" class="active"><a data-toggle="tab" href="#pstConfigProducto"><h5><b>Administración de Producto</b></h5></a></li>
                            <li id="pstConfigCategoriasPrecios"><a data-toggle="tab" href="#pstCategoriasPrecios"><h5><b>Categorias de Precios</b></h5></a></li>
                            <li id="pstConfigCanalImpresion"><a data-toggle="tab" href="#pstCanalImpresion"><h5><b>MaxPoint</b></h5></a></li>
                            <li id="pstConfigPreguntas"><a data-toggle="tab" href="#menu2"><h5><b>Preguntas Sugeridas</b></h5></a></li>
                            <li id="pstConfigPoliticas"><a data-toggle="tab" href="#pstPoliticas"><h5><b>Políticas de Configuración</b></h5></a></li>
                        </ul>
                        <div id="cntParametrosConfiguracion" class="tab-content">
                            <div id="pstConfigProducto" class="tab-pane fade in active center-block">
                                <br/>
                                <div class="row">
                                    <div class="col-md-8"></div>
                                    <div class="col-md-2 text-right">
                                        <div class="btn-group">
                                            <h5 class="text-right"><b>Está Activo? <input inputmode="none"  type="checkbox" id="inEstadoProducto"></b></h5>
                                        </div>
                                    </div>
                                    <div class="col-md-1"></div>
                                </div>
                                <div class="row">
                                    <div class="col-xs-3 text-right"><h6><b>Descripción:</b></h6></div>
                                    <div class="col-xs-7">
                                        <div class="form-group">
                                            <input inputmode="none"  type="text" id="inDescripcionProducto" class="form-control"/>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xs-3 text-right"><h6><b>Clasificación:</b></h6></div>
                                    <div class="col-xs-7">
                                        <div class="form-group">
                                            <select id="inClasificacionProducto" class="form-control"></select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xs-3 text-right"><h6><b>Tipo de Producto:</b></h6></div>
                                    <div class="col-xs-3">
                                        <div class="form-group">
                                            <select id="inTipoProducto" class="form-control"></select>
                                        </div>
                                    </div>
                                    <div class="col-xs-2 text-right"><h6><b>Preparación (min):</b></h6></div>
                                    <div class="col-xs-2">
                                        <div class="form-group">
                                            <input inputmode="none"  class="form-control" type="text" id="inPreparacionProducto"/>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xs-3 text-right"><h6><b>Modificador:</b></h6></div>
                                    <div class="col-xs-7">
                                        <div class="form-group">
                                            <select id="inModificador" class="form-control"></select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xs-3 text-right"><h6><b>Impuestos:</b></h6></div>
                                    <div class="col-xs-7">
                                        <div class="form-group">
                                            <select id="inImpuestosProducto" data-placeholder="Seleccionar Impuestos" multiple="multiple" class="chosen-select"></select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-xs-3 text-right"><h6><b>Departamento:</b></h6></div>
                                    <div class="col-xs-7">
                                        <div class="form-group">
                                            <select id="inDepartamento" class="form-control"></select>
                                        </div>
                                    </div> 
                                </div>

                                <div class="row">
                                    <div class="col-xs-3 text-right"><h6><b>Contenido:</b></h6></div>
                                    <div class="col-xs-7">
                                        <div class="form-group">
                                            <textArea id="inContenidoProducto" class="form-control noResizable" rows="3"></textArea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div id="pstCategoriasPrecios" class="tab-pane fade">
                                <br/>
                                <div class="row">
                                    <div class="col-xs-2"></div>
                                    <div class="col-xs-2 text-right">
                                        <h5><b>Categorias de Precios</b></h5>
                                    </div>
                                    <div class="col-xs-2">
                                        <h5><b>Pvp</b></h5>
                                    </div>
                                    <div id="cntEtiquetaNeto" class="col-xs-2">
                                        <h5><b>Neto</b></h5>
                                    </div>
                                    <div id="cntEtiquetaIva" class="col-xs-2">
                                        <h5><b>Iva</b></h5>
                                    </div>
                                </div>
                                <div id="listaCategoriasPrecios"></div>
                            </div>
                            <div id="pstCanalImpresion" class="tab-pane fade">
                                <br/>
                                <div class="row">
                                    <div class="col-xs-1"></div>
                                    <div class="col-xs-3">
                                        <h5><b>Configuraciones MaxPoint</b></h5>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xs-1"></div>
                                    <div class="col-xs-2 text-right"><h6><b>Canal de Impresión:</b></h6></div>
                                    <div class="col-xs-6">
                                        <div class="form-group">
                                            <select id="inCanalImpresionProducto" data-placeholder="Seleccionar Canales de Impresión" multiple="multiple" class="chosen-select"></select>
                                        </div>
                                    </div>                                                
                                </div>
                                <div class="row">
                                    <div class="col-xs-3 text-right"><h6><b>Administrar Stock</b></h6></div>
                                    <div class="col-xs-2">
                                        <div class="form-group">
                                            <input inputmode="none"  type="checkbox" id="inCantidadProducto" data-off-text="No" data-on-text="Si"/>
                                        </div>
                                    </div>
                                    <div class="col-xs-1"></div>
                                    <div class="col-xs-2"><h6><b>Autorización Anular</b></h6></div>
                                    <div class="col-xs-2">
                                        <div class="form-group">
                                            <input inputmode="none"  type="checkbox" id="inAnulacionProducto" data-off-text="No" data-on-text="Si"/>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xs-3 text-right"><h6><b>Display Cocina</b></h6></div>
                                    <div class="col-xs-2">
                                        <div class="form-group">
                                            <input inputmode="none"  type="checkbox" id="inQsrProducto" data-off-text="No" data-on-text="Si"/>
                                        </div>
                                    </div>
                                    <div class="col-xs-1"></div>
                                    <div class="col-xs-2 text-right"><h6><b>Peso en Gramos</b></h6></div>
                                    <div class="col-xs-2">
                                        <div class="form-group">
                                            <input inputmode="none"  type="checkbox" id="inGramosProducto" data-off-text="No" data-on-text="Si"/>
                                        </div>
                                    </div>
                                </div>
                                <br/>
                                <div class="row">
                                    <div class="col-xs-3 text-right"><h6><b>Parametro Búsqueda:</b></h6></div>
                                    <div class="col-xs-5">
                                        <div class="form-group">
                                            <input inputmode="none"  type="text" id="inParametroMasterPluProducto" class="form-control" idMasterPlu="" placeholder="Descripción o NumPlu"/>
                                        </div>
                                    </div>
                                    <div class="col-xs-3">
                                        <div class="form-group">
                                            <button type="button" class="btn btn-default" aria-label="Left Align" onclick="cargarMasterPlus()">
                                                <span class="glyphicon glyphicon-search" aria-hidden="true"></span> Buscar
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xs-1"></div>
                                    <div class="col-xs-2 text-right"><h6><b>Productos:</b></h6></div>
                                    <div class="col-xs-7">
                                        <div class="form-group">
                                            <select id="inMasterPlus" onchange="changeMasterPlu()" data-placeholder="Master Plu" class="form-control"></select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xs-3 text-right"><h6><b>Master Plu:</b></h6></div>
                                    <div class="col-xs-7">
                                        <div class="form-group">
                                            <input inputmode="none"  type="text" id="inMasterPluProducto" class="form-control" idMasterPlu="" disabled="disabled"/>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xs-3 text-right"><h6><b>Código de barras:</b></h6></div>
                                    <div class="col-xs-7">
                                        <div class="form-group">
                                            <input inputmode="none"  class="form-control" type="text" id="inCodigoBarrasProducto"/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div id="menu2" class="tab-pane fade">
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-xs-6"><h5><b>Preguntas Sugeridas por Cadena</b></h5></div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xs-6"><h6><b>Lista Preguntas Sugeridas</b></h6></div>
                                        <div class="col-xs-6"><h6><b>Agregadas</b></h6></div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xs-6">
                                            <div style="height:290px; overflow:auto;">
                                                <table id="listaPreguntasSugeridas" class="table table-bordered"></table>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div style="height:290px; overflow:auto;">
                                                <table id="listaPreguntasRelacionadas" class="table table-bordered"></table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div id="pstPoliticas" role="tabpanel" class="tab-pane fade" align="center">
                                <br/>
                                <div class="panel panel-default" style="width:1060px;" align="center">
                                    <div class="panel-heading">
                                        <div class="row">
                                            <div class="col-xs-10 col-md-9"><h6><b>Plus Colección de Datos</b></h6></div>
                                            <div class="col-md-1"></div>
                                            <div>
                                                <button id="btnPluAgregarColeccionPlus" type="button" class="btn btn-default" onclick="nuevaColeccionPlus(null);" >
                                                    <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
                                                </button>
                                                <button id="btnPluEditarColeccionPlus" type="button" class="btn btn-default" onclick="editarColeccionPlus(null, null, null);">
                                                    <span class="glyphicon glyphicon-pencil" style="opacity: 0;"
                                                          aria-hidden="true"></span>
                                                </button>
                                            </div>
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
                                        <div class="form-group" id="detalleColeccionPlus">
                                            <table id="tblColeccionPlus" class="table table-bordered table-fixed-layout" style="font-size: 11px;"></table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> <!-- Fin contenedor_configuraciones -->
                    <div class="modal-footer panel-footer">
                        <button id="btnGuardarCambios" type="button" class="btn btn-primary" onclick="">Guardar</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                    </div>
                </div>
            </div>
        </div>
        
        <div id="mdlPluEditarColeccion" class="modal fade" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static">
            <div class="modal-dialog" style="width: 700px;">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Colección:
                            <label id="mdlPluEditarColeccionTitulo" data-idColeccionPlus="" data-idColeccionDeDatosPlus="" data-idPlu=""></label>
                        </h4>
                    </div>
                    <div class="modal-body">
                        <div class="col-xs-12 text-right"><h6>Activo:&nbsp;&nbsp;&nbsp;<input inputmode="none"  id="inpPluEditarEstado" type="checkbox" checked="checked"/></h6></div>
                        <div id="pluEditarTiposDato">
                            <div class="row">
                                <div class="col-xs-6 col-md-4">
                                    <div class="btn-group">
                                        <h5 class="text-right">Especifica Valor: <input inputmode="none"  id="inpPluEditarEspecificaValor" disabled="disabled" type="checkbox"/></h5>
                                    </div>
                                </div>
                                <div class="col-xs-6 col-md-4">
                                    <div class="btn-group">
                                        <h5 class="text-right">Obligatorio: <input inputmode="none"  id="inpPluEditarObligatorio" disabled="disabled" type="checkbox"/></h5>
                                    </div>
                                </div>
                                <div class="col-xs-6 col-md-4">
                                    <div class="btn-group">
                                        <h5 class="text-right">Tipo de dato: <label id="lblPluEditarTipoDato" value="1"></label></h5>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-3 text-right"><h5>Varchar:</h5></div>
                                <div class="col-xs-7">
                                    <div class="form-group">
                                        <input inputmode="none"  id="inpPluEditarCaracter" type="text" class="form-control" />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-3 text-right"><h5>Entero:</h5></div>
                                <div class="col-xs-4">
                                    <div class="form-group">
                                        <input inputmode="none"  id="inpPluEditarEntero" maxlength="50" type="text" class="form-control"/>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-3 text-right"><h5>Fecha:</h5></div>
                                <div class="col-xs-4">
                                    <div class="form-group">
                                        <div class="input-prepend input-group">
                                            <span class="add-on input-group-addon"><i class="glyphicon glyphicon-calendar fa fa-calendar"></i></span>
                                            <input inputmode="none"  id="inpPluEditarFecha" type="text" value="" class="form-control" placeholder="Fecha" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">                     	
                                <div class="col-xs-3 text-right"><h5>Selección:</h5></div>
                                <div class="col-xs-3">
                                    <div class="form-group">
                                        <input inputmode="none"  id="inpPluEditarSeleccion" type="checkbox" data-off-text="No" data-on-text="Si" />         
                                    </div>
                                </div>                             
                            </div>
                            <div class="row">
                                <div class="col-xs-3 text-right"><h5>Numérico:</h5></div>
                                <div class="col-xs-4">
                                    <div class="form-group">
                                        <input inputmode="none"  id="inpPluEditarNumerico" maxlength="50" type="text" class="form-control"/>
                                    </div>
                                </div>
                            </div>                   
                            <div class="row">                      
                                <div class="col-xs-3 text-right"><h5 class="text-right">Rango Fecha:</h5></div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="inpPluEditarFechaIni" class="control-label">Fecha Inicio</label>
                                        <div class="input-prepend input-group">
                                            <span class="add-on input-group-addon"><i class="glyphicon glyphicon-calendar fa fa-calendar"></i></span>
                                            <input inputmode="none"  id="inpPluEditarFechaIni" type="text" value="" class="form-control" placeholder="Fecha Inicio" />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="inpPluEditarFechaFin" class="control-label">Fecha Fin</label>
                                        <div class="input-prepend input-group">
                                            <span class="add-on input-group-addon"><i class="glyphicon glyphicon-calendar fa fa-calendar"></i></span>
                                            <input inputmode="none"  id="inpPluEditarFechaFin" type="text" value="" class="form-control" placeholder="Fecha Fin" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3"><h5 class="text-right">Rango Decimal:</h5></div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="inpPluEditarMin" class="control-label">Mínimo</label>
                                        <div class="form-group">
                                            <input inputmode="none"  id="inpPluEditarMin" maxlength="50" type="text" class="form-control" />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="inpPluEditarMax" class="control-label">Máximo</label>
                                        <div class="form-group">
                                            <input inputmode="none"  id="inpPluEditarMax" maxlength="50" type="text" class="form-control" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal" onclick="cancelar()">Cancelar</button>
                        <button type="button" class="btn btn-primary" onclick="validarEditarColeccionPlus();">Guardar</button>
                    </div>
                </div>
            </div>
        </div>
        <div id="mdlPluNuevaColeccion" class="modal fade" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static" aria-hidden="true" >
            <div class="modal-dialog" style="width: 700px;">
                <div class="modal-content">
                    <div class="modal-header">                  
                        <h4 class="modal-title">Colección:
                            <label id="mdlPluNuevaColeccionTitulo"></label>
                        </h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">                   
                            <div class="col-xs-12 col-md-12">
                                <div class="col-xs-12 col-md-6">
                                    <div class="form-group" style="height: 200px; overflow: auto;">
                                        <table id="tblPluColeccionDescripcion" class="table table-bordered" style="font-size: 11px;"></table>                                
                                    </div>
                                </div>
                                <div class="col-xs-12 col-md-6">
                                    <div class="form-group" style="height: 200px; overflow: auto;">
                                        <table id="tblPluColeccionDatos" class="table table-bordered" style="font-size: 11px;"></table>                                
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="divPluColeccionTiposDato" style="height: 180px; overflow: auto;">
                            <div class="row">
                                <div class="col-xs-6 col-md-4">
                                    <div class="btn-group">
                                        <h5 class="text-right">Especifica Valor: <input inputmode="none"  id="inpPluNuevoEspecificaValor" disabled="disabled" type="checkbox" value="1"/></h5>
                                    </div>
                                </div>
                                <div class="col-xs-6 col-md-4">
                                    <div class="btn-group">
                                        <h5 class="text-right">Obligatorio: <input inputmode="none"  id="inpPluNuevoObligatorio" disabled="disabled" type="checkbox" value="1"/></h5>
                                    </div>
                                </div>
                                <div class="col-xs-6 col-md-4">
                                    <div class="btn-group">
                                        <h5 class="text-right">Tipo de dato: <label id="lblPluNuevoTipoDato" value="1"></label></h5>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-3 text-right"><h5>Varchar:</h5></div>
                                <div class="col-xs-7">
                                    <div class="form-group">
                                        <input inputmode="none"  id="inpPluNuevoCaracter" type="text" class="form-control"/>
                                    </div>
                                </div>
                            </div> 
                            <div class="row">
                                <div class="col-xs-3 text-right"><h5>Entero:</h5></div>
                                <div class="col-xs-4">
                                    <div class="form-group">
                                        <input inputmode="none"  id="inpPluNuevoEntero" maxlength="50" type="text" class="form-control" />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-3 text-right"><h5>Fecha:</h5></div>
                                <div class="col-xs-4">
                                    <div class="form-group">
                                        <div class="input-prepend input-group">
                                            <span class="add-on input-group-addon"><i class="glyphicon glyphicon-calendar fa fa-calendar"></i></span>
                                            <input inputmode="none"  id="inpPluNuevoFecha" type="text" value="" class="form-control" placeholder="Fecha" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-3 text-right"><h5>Selección:</h5></div>
                                <div class="col-xs-3">
                                    <div class="form-group">
                                        <input inputmode="none"  id="inpPluNuevoSeleccion" type="checkbox" data-off-text="No" data-on-text="Si" />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-3 text-right"><h5>Numérico:</h5></div>
                                <div class="col-xs-4">
                                    <div class="form-group">
                                        <input inputmode="none"  id="inpPluNuevoNumerico" maxlength="50" type="text" class="form-control" />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3"><h5 class="text-right">Rango Fecha:</h5></div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="inpPluNuevoFechaIni" class="control-label">Fecha Inicio</label>
                                        <div class="input-prepend input-group">
                                            <span class="add-on input-group-addon"><i class="glyphicon glyphicon-calendar fa fa-calendar"></i></span>
                                            <input inputmode="none"  id="inpPluNuevoFechaIni" type="text" value="" class="form-control" name="FechaInicial" placeholder="Fecha Inicio" />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="inpPluNuevoFechaFin" class="control-label">Fecha Fin</label>
                                        <div class="input-prepend input-group">
                                            <span class="add-on input-group-addon"><i class="glyphicon glyphicon-calendar fa fa-calendar"></i></span>
                                            <input inputmode="none"  id="inpPluNuevoFechaFin" type="text" value="" class="form-control" name="FechaFinal" placeholder="Fecha Fin" />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-3"><h5 class="text-right">Rango Decimal:</h5></div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="inpPluNuevoMin" class="control-label">Mínimo</label>
                                        <div class="form-group">
                                            <input inputmode="none"  id="inpPluNuevoMin" maxlength="50" type="text" class="form-control" />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="inpPluNuevoMax" class="control-label">Máximo</label>
                                        <div class="form-group">
                                            <input inputmode="none"  id="inpPluNuevoMax" maxlength="50" type="text" class="form-control" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal" onclick="cancelar();">Cancelar</button>
                        <button id="btnPluNuevaColeccionPlus" type="button" class="btn btn-primary" onclick="validarNuevaColeccionPlus(null, null, null);">Guardar</button>
                    </div>
                </div>
            </div>
        </div>

        <div id="mdl_rdn_pdd_crgnd" class="modal_cargando" ng-show="cargando">
            <div id="mdl_pcn_rdn_pdd_crgnd" class="modal_cargando_contenedor">
                <img src="../../imagenes/admin_resources/progressBar.gif" />
            </div>
        </div>
        
        <input inputmode="none"  id="especificaValor" type="hidden" value="0">
        <input inputmode="none"  id="obligatorio" type="hidden" value="0">

        <script type="text/javascript" src="../../js/jquery1.11.1.js"></script>
        <script type="text/javascript" src="../../js/jquery-latest.min.js"></script>
        <script type="text/javascript" src="../../js/ajax_datatables.js"></script>
        <script type="text/javascript" src="../../js/jquery-sortable.js"></script>
        <script type="text/javascript" src="../../js/jquery.uitablefilter.js"></script>
        <script type="text/javascript" src="../../bootstrap/js/moment.js"></script>
        <script type="text/javascript" src="../../bootstrap/js/daterangepicker.js"></script>   
        <script type="text/javascript" src="../../bootstrap/js/bootstrap.js"></script>
        <script type="text/javascript" src="../../bootstrap/js/bootstrap-dataTables.js"></script>
        <script type="text/javascript" src="../../bootstrap/js/switch.js"></script>
        <script type="text/javascript" src="../../js/alertify.js"></script>
        <script type="text/javascript" src="../../js/calendario.js"></script>
        <script type="text/javascript" src="../../js/idioma.js"></script>
        <script type="text/javascript" src="../../js/chosen.jquery.min.js"></script>
        <script type="text/javascript" src="../../js/chosen.proto.min.js"></script>
        <script type="text/javascript" src="../../js/ajax_productos.js"></script>
    </body>
</html>