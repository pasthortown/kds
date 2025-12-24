<!DOCTYPE html>
<html lang="es" ng-app="app">
    <head>
        <meta charset="utf-8">
        <title>Políticas</title>
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1 user-scalable=no">

        <!-- CSS -->
        <link rel="stylesheet" href="../../bootstrap/normalize/normalize.css" media="all"/>
        <link rel="stylesheet" href="../../bootstrap/templete/css/icons.css" media="all"/>
        <link rel="stylesheet" href="../../bootstrap/templete/css/bootstrap.css" media="all"/>
        <link rel="stylesheet" href="../../bootstrap/templete/css/plugins.css" media="all"/>
        <link rel="stylesheet" href="../../bootstrap/templete/css/main.css" media="all"/>
        <link rel="stylesheet" href="../../bootstrap/templete/css/custom.css" media="all"/>
        <link rel="stylesheet" href="../../css/politicas.css" media="all"/>

    </head>
    <body ng-controller="pntColeccion">

        <div class="page-content-wrapper">

            <!-- Cabecera -->
            <div class="page-content-inner small">
                <div id="page-header" class="clearfix">
                    <div class="page-header">
                        <h4>&nbsp;&nbsp;&nbsp;Administración de Políticas</h4>
                        <!--Formulario Colecciones-->
                        <form class="form-inline" role="form">
                            <div class="form-group">
                                <div class="btn-group">
                                    <div class="row">
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        <button class="btn btn-primary" type="button" ng-class="{'active': modelColeccion === 'Pais'}" ng-click="cargarColeccion('Pais')">País</button>
                                        <button class="btn btn-primary" type="button" ng-class="{'active': modelColeccion === 'Cadena'}" ng-click="cargarColeccion('Cadena')">Cadena</button>
                                        <button class="btn btn-primary" type="button" ng-class="{'active': modelColeccion === 'Restaurante'}" ng-click="cargarColeccion('Restaurante')">Restaurante</button>
                                        <button class="btn btn-primary" type="button" ng-class="{'active': modelColeccion === 'Estacion'}" ng-click="cargarColeccion('Estacion')">Estación</button>
                                        <button class="btn btn-primary" type="button" ng-class="{'active': modelColeccion === 'Formapago'}" ng-click="cargarColeccion('Formapago')">Formas de Pago</button>
                                        <button class="btn btn-primary" type="button" ng-class="{'active': modelColeccion === 'Botones'}" ng-click="cargarColeccion('Botones')">Botones</button>
                                        <button class="btn btn-primary" type="button" ng-class="{'active': modelColeccion === 'Plus'}" ng-click="cargarColeccion('Plus')">Plus</button>
                                        <button class="btn btn-primary" type="button" ng-class="{'active': modelColeccion === 'Usuarios'}" ng-click="cargarColeccion('Usuarios')">Usuarios</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <br/>
                    </div>
                </div>
            </div>

            <!-- GridColeccion -->
            <div class="row" ng-controller="GridColeccion">
                <!-- Fin Contenedor Colecciones -->
                <div class="col-md-12 sortable-layout">
                    <div class="panel panel-default toggle panelMove panelClose panelRefresh">
                        <div class="panel-heading">
                            <h4 class="panel-title">Colecciones de Datos {{modelColeccion}}</h4>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <!-- Contenedor Colecciones -->
                                <div class="col-lg-6 col-md-6">
                                    <div class="row">
                                        <div class="col-lg-7 col-md-7">
                                            <button class="btn btn-success" type="button" ng-click="openModalCreateColeccion()">
                                                <i class="glyphicon glyphicon-plus mr10"></i>Nueva Colección
                                            </button>
                                        </div>
                                        <div class="col-lg-5 col-md-5">
                                            <div class="input-group">
                                                <span class="input-group-addon">
                                                    <i class="fa fa-search"></i>
                                                </span>
                                                <input inputmode="none"  type="text" class="form-control" ng-model="busquedaColeccion" value="{{busquedaColeccion}}"/>
                                            </div>

                                        </div>
                                    </div>
                                    <table id="tblColecciones" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                        <thead>
                                            <tr>
                                                <th class="center">COLECCIÓN</th>
                                                <th class="center">CONFIGURACIÓN</th>
                                                <th class="center">REPORTE</th>
                                                <th class="center">CUBO</th>
                                                <th class="center">REPETIR</th>
                                                <th class="center">ACTIVO</th>
                                            </tr>
                                        </thead>
                                        <tbody ng-repeat="datos in colecciones | filter :busquedaColeccion">
                                            <tr ng-class="{'success': datos.idColeccion === coleccionSelected.idColeccion}" ng-click="cgrParametros(datos)" ng-dblclick="openModalUpdateColeccion()">
                                                <td>{{datos.descripcion}}</td>
                                                <td class="text-center">
                                                    <input inputmode="none"  type="checkbox" ng-true-value="1" ng-false-value="0" ng-model="datos.configuracion" disabled="disabled">
                                                </td>
                                                <td class="text-center">
                                                    <input inputmode="none"  type="checkbox" ng-true-value="1" ng-false-value="0" ng-model="datos.reporte" disabled="disabled">
                                                </td>
                                                <td class="text-center">
                                                    <input inputmode="none"  type="checkbox" ng-true-value="1" ng-false-value="0" ng-model="datos.cubo" disabled="disabled">
                                                </td>
                                                <td class="text-center">
                                                    <input inputmode="none"  type="checkbox" ng-true-value="1" ng-false-value="0" ng-model="datos.repetirConfiguracion" disabled="disabled">
                                                </td>
                                                <td class="text-center">
                                                    <input inputmode="none"  type="checkbox" ng-true-value="1" ng-false-value="0" ng-model="datos.activo" disabled="disabled">
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <!-- Fin Contenedor Colecciones -->

                                    <!-- Modal Colleccion -->
                                    <div class="modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" ng-style="modalColeccion">
                                        <div class="modal-dialog quitarMarginTop5">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal" ng-click="closeModalColeccion()">
                                                        <span aria-hidden="true">&times;</span><span class="sr-only">Close</span>
                                                    </button>
                                                    <h4 class="modal-title" id="mySmallModalLabel">Colección {{coleccionSelected.descripcion}}</h4>
                                                </div>
                                                <div class="modal-body quitarPaddingBottom">
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <div class="panel panel-default">
                                                                <div class="panel-body pt0 pb0">
                                                                    <form class="form-horizontal">
                                                                        <div class="form-group">
                                                                            <div class="col-lg-8 col-md-6"></div>
                                                                            <label class="col-lg-2 col-md-3 control-label" for="inParColEstado"><b>Activo</b></label>
                                                                            <div class="col-lg-2 col-md-3">
                                                                                <div class="toggle-custom">
                                                                                    <label class="toggle" data-on="SI" data-off="NO">
                                                                                        <input inputmode="none"  type="checkbox" id="inParColEstado" name="checkbox-toggle" ng-true-value="1" ng-false-value="0" ng-model="cloneColeccion.activo">
                                                                                        <span class="button-checkbox"></span>
                                                                                    </label>
                                                                                </div>
                                                                            </div>
                                                                            <label class="col-lg-2 col-md-3 control-label" for="inColDescripcion"><b>Colección</b></label>
                                                                            <div class="col-lg-10 col-md-9">
                                                                                <input inputmode="none"  id="inColDescripcion" type="text" class="form-control" name="default" ng-model="cloneColeccion.descripcion" value="{{cloneColeccion.descripcion}}" required>
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label class="col-lg-2 col-md-3 control-label" for="inParModulo"><b>Módulo</b></label>
                                                                            <div class="col-lg-10 col-md-9">
                                                                                <select id="inParModulo" class="form-control" ng-model="cloneColeccion.idModulo">
                                                                                    <option ng-repeat="modulo in modulos" value="{{modulo.idModulo}}">{{modulo.descripcion}}</option>
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label class="col-lg-2 col-md-3 control-label" for="inParConfiguracion"><b>Configuración</b></label>
                                                                            <div class="col-lg-2 col-md-3">
                                                                                <div class="toggle-custom">
                                                                                    <label class="toggle" data-on="SI" data-off="NO">
                                                                                        <input inputmode="none"  type="checkbox" id="inParConfiguracion" name="checkbox-toggle" ng-true-value="1" ng-false-value="0" ng-model="cloneColeccion.configuracion">
                                                                                        <span class="button-checkbox"></span>
                                                                                    </label>
                                                                                </div>
                                                                            </div>
                                                                            <label class="col-lg-2 col-md-3 control-label" for="inParReporte"><b>Reporte</b></label>
                                                                            <div class="col-lg-2 col-md-3">
                                                                                <div class="toggle-custom">
                                                                                    <label class="toggle" data-on="SI" data-off="NO">
                                                                                        <input inputmode="none"  type="checkbox" id="inParReporte" name="checkbox-toggle" ng-true-value="1" ng-false-value="0" ng-model="cloneColeccion.reporte">
                                                                                        <span class="button-checkbox"></span>
                                                                                    </label>
                                                                                </div>
                                                                            </div>
                                                                            <label class="col-lg-2 col-md-3 control-label" for="inParCubo"><b>Cubo</b></label>
                                                                            <div class="col-lg-2 col-md-3">
                                                                                <div class="toggle-custom">
                                                                                    <label class="toggle" data-on="SI" data-off="NO">
                                                                                        <input inputmode="none"  type="checkbox" id="inParCubo" name="checkbox-toggle" ng-true-value="1" ng-false-value="0" ng-model="cloneColeccion.cubo">
                                                                                        <span class="button-checkbox"></span>
                                                                                    </label>
                                                                                </div>
                                                                            </div>
                                                                            <label class="col-lg-2 col-md-3 control-label" for="inParRepetirConfiguracion"><b>Repertir Configuración</b></label>
                                                                            <div class="col-lg-2 col-md-3">
                                                                                <div class="toggle-custom">
                                                                                    <label class="toggle" data-on="SI" data-off="NO">
                                                                                        <input inputmode="none"  type="checkbox" id="inParRepetirConfiguracion" name="checkbox-toggle" ng-true-value="1" ng-false-value="0" ng-model="cloneColeccion.repetirConfiguracion">
                                                                                        <span class="button-checkbox"></span>
                                                                                    </label>
                                                                                </div>
                                                                            </div>
                                                                            <label class="col-lg-2 col-md-3 control-label" for="inParEstado1"><b>Estado 1</b></label>
                                                                            <div class="col-lg-2 col-md-3">
                                                                                <div class="toggle-custom">
                                                                                    <label class="toggle" data-on="SI" data-off="NO">
                                                                                        <input inputmode="none"  type="checkbox" id="inParEstado1" name="checkbox-toggle" ng-true-value="1" ng-false-value="0" ng-model="cloneColeccion.estado1">
                                                                                        <span class="button-checkbox"></span>
                                                                                    </label>
                                                                                </div>
                                                                            </div>
                                                                            <label class="col-lg-2 col-md-3 control-label" for="inParEstado2"><b>Estado 2</b></label>
                                                                            <div class="col-lg-2 col-md-3">
                                                                                <div class="toggle-custom">
                                                                                    <label class="toggle" data-on="SI" data-off="NO">
                                                                                        <input inputmode="none"  type="checkbox" id="inParEstado2" name="checkbox-toggle" ng-true-value="1" ng-false-value="0" ng-model="cloneColeccion.estado2">
                                                                                        <span class="button-checkbox"></span>
                                                                                    </label>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label class="col-lg-2 col-md-3 control-label" for="inColIntegracion"><b>Tabla Integración</b></label>
                                                                            <div class="col-lg-10 col-md-9">
                                                                                <select id="inColIntegracion" class="form-control" ng-model="cloneColeccion.descripcionIntegracion" ng-change="cargarRegistrosTablaIntegracionColeccion()">
                                                                                    <option ng-repeat="tablaIntegracion in tablasIntegracion" value="{{tablaIntegracion.idTabla}}">{{tablaIntegracion.tabla}}</option>
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label class="col-lg-2 col-md-3 control-label" for="inColIdIntegracion"><b>Registro Integración</b></label>
                                                                            <div class="col-lg-10 col-md-9">
                                                                                <select id="inColIdIntegracion" class="form-control" ng-model="cloneColeccion.idIntegracion">
                                                                                    <option ng-repeat="fila in registrosIntegracion" value="{{fila.id}}">{{fila.descripcion}}</option>
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label class="col-lg-2 col-md-3  control-label" for="clObservacion"><b>Observiciones</b></label>
                                                                            <div class="col-lg-10 col-md-9">
                                                                                <div class="input-group input-icon">
                                                                                    <textarea id="clObservacion" class="form-control" rows="3" ng-model="cloneColeccion.observaciones" required>{{cloneColeccion.observaciones}}</textarea>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label class="col-lg-2 col-md3 control-label"><b>Modificado</b></label>
                                                                            <div class="col-lg-3 col-md-3"><p class="form-control-static">{{cloneColeccion.usuarioModifico}}</p></div>
                                                                            <div class="col-lg-3 col-md-3"><p class="form-control-static">{{cloneColeccion.fechaModificado}}</p></div>
                                                                            <div class="col-lg-3 col-md-3"><p class="form-control-static">{{cloneColeccion.horaModificado}}</p></div>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!-- End Formulario -->
                                                    </div>
                                                </div>
                                                <div class="modal-footer quitarMarginTop">
                                                    <center>
                                                        <button type="button" class="btn btn-default" ng-click="closeModalColeccion()">Cancelar</button>
                                                        <button type="button" class="btn btn-primary" ng-click="changeColeccion()">
                                                            <i class="glyphicon glyphicon-floppy-saved mr10"></i>Guardar
                                                        </button>
                                                    </center>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Contenedor Parametros -->
                                <div class="col-md-6 sortable-layout" ng-controller="GridParametro">
                                    <div class="row">
                                        <div class="col-lg-7 col-md-7">
                                            <button class="btn btn-success" ng-click="openModalCreateParametro()">
                                                <i class="glyphicon glyphicon-plus mr10"></i>Nueva Parametro
                                            </button>
                                        </div>
                                        <div class="col-lg-5 col-md-5">
                                            <div class="input-group">
                                                <span class="input-group-addon">
                                                    <i class="fa fa-search"></i>
                                                </span>
                                                <input inputmode="none"  type="text" class="form-control" ng-model="busquedaParametro.descripcion" value="{{busquedaParametro.descripcion}}"/>
                                            </div>

                                        </div>
                                    </div>
                                    <table id="tblParametros" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                        <thead>
                                            <tr>
                                                <th class="center">PARAMETRO</th>
                                                <th class="center">ESP. VALOR</th>
                                                <th class="center">OBLIGATORIO</th>
                                                <th class="center">TIPO DATO</th>
                                                <th class="center">ACTIVO</th>
                                            </tr>
                                        </thead>
                                        <tbody ng-repeat="dato in parametros|filter:busquedaParametro.descripcion">
                                            <tr ng-class="{'info' : dato.idParametro === parametroSelected.idParametro}" ng-click="cgrSelectParametro(dato)" ng-dblclick="openModalUpdateParametro(dato)">
                                                <td>{{dato.descripcion}}</td>
                                                <td class="text-center">
                                                    <input inputmode="none"  type="checkbox" ng-true-value="1" ng-false-value="0" ng-model="dato.especificarValor" disabled="disabled">
                                                </td>
                                                <td class="text-center">
                                                    <input inputmode="none"  type="checkbox" ng-true-value="1" ng-false-value="0" ng-model="dato.obligatorio" disabled="disabled">
                                                </td>
                                                <td class="text-center">{{dato.descripcionTipoDato}}</td>
                                                <td class="text-center">
                                                    <input inputmode="none"  type="checkbox" ng-true-value="1" ng-false-value="0" ng-model="dato.activo" disabled="disabled">
                                                </td>
                                            </tr>
                                        </tbody>
                                        <!-- End Tabla Parametros -->
                                    </table>

                                    <!-- Modal Parametros -->
                                    <div class="modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" ng-style="modalParametro">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal" ng-click="closeModalParametro()">
                                                        <span aria-hidden="true">&times;</span><span class="sr-only">Close</span>
                                                    </button>
                                                    <h4 class="modal-title" id="mySmallModalLabel">Parametro {{parametroSelected.descripcion}}</h4>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <div class="panel panel-default">
                                                                <div class="panel-body pt0 pb0">
                                                                    <form class="form-horizontal">
                                                                        <div class="form-group">
                                                                            <div class="col-lg-8 col-md-6"></div>
                                                                            <label class="col-lg-2 col-md-3 control-label" for="inParEstado"><b>Activo</b></label>
                                                                            <div class="col-lg-2 col-md-3">
                                                                                <div class="toggle-custom">
                                                                                    <label class="toggle" data-on="SI" data-off="NO">
                                                                                        <input inputmode="none"  type="checkbox" id="inParEstado" name="checkbox-toggle" ng-true-value="1" ng-false-value="0" ng-model="cloneParametro.activo">
                                                                                        <span class="button-checkbox"></span>
                                                                                    </label>
                                                                                </div>
                                                                            </div>
                                                                            <label class="col-lg-2 col-md-3 control-label" for="inParDescripcion"><b>Parametro</b></label>
                                                                            <div class="col-lg-10 col-md-9">
                                                                                <input inputmode="none"  id="inParDescripcion" type="text" class="form-control" name="default" ng-model="cloneParametro.descripcion" value="{{cloneParametro.descripcion}}">
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label class="col-lg-2 col-md-3 control-label" for="inParTipoDato"><b>Tipo de Dato</b></label>
                                                                            <div class="col-lg-10 col-md-9">
                                                                                <select id="inParTipoDato" class="form-control" ng-model="cloneParametro.tipoDato">
                                                                                    <option ng-repeat="tipo in tiposDatos" value="{{tipo.tipoDato}}">{{tipo.descripcionTipoDato}}</option>
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label class="col-lg-2 col-md-3 control-label" for="inParEspecificarValor"><b>Especificar Valor</b></label>
                                                                            <div class="col-lg-2 col-md-3">
                                                                                <div class="toggle-custom">
                                                                                    <label class="toggle" data-on="SI" data-off="NO">
                                                                                        <input inputmode="none"  type="checkbox" id="inParEspecificarValor" name="checkbox-toggle" ng-true-value="1" ng-false-value="0" ng-model="cloneParametro.especificarValor">
                                                                                        <span class="button-checkbox"></span>
                                                                                    </label>
                                                                                </div>
                                                                            </div>
                                                                            <label class="col-lg-2 col-md-3 control-label" for="inParObligatorio"><b>Obligatorio</b></label>
                                                                            <div class="col-lg-2 col-md-3">
                                                                                <div class="toggle-custom">
                                                                                    <label class="toggle" data-on="SI" data-off="NO">
                                                                                        <input inputmode="none"  type="checkbox" id="inParObligatorio" name="checkbox-toggle" ng-true-value="1" ng-false-value="0" ng-model="cloneParametro.obligatorio">
                                                                                        <span class="button-checkbox"></span>
                                                                                    </label>
                                                                                </div>
                                                                            </div>
                                                                            <label class="col-lg-2 col-md-3 control-label" for="inParParEstado1"><b>Estado 1</b></label>
                                                                            <div class="col-lg-2 col-md-3">
                                                                                <div class="toggle-custom">
                                                                                    <label class="toggle" data-on="SI" data-off="NO">
                                                                                        <input inputmode="none"  type="checkbox" id="inParParEstado1" name="checkbox-toggle" ng-true-value="1" ng-false-value="0" ng-model="cloneParametro.estado1">
                                                                                        <span class="button-checkbox"></span>
                                                                                    </label>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label class="col-lg-2 col-md-3 control-label" for="inParParEstado2"><b>Estado 2</b></label>
                                                                            <div class="col-lg-2 col-md-3">
                                                                                <div class="toggle-custom">
                                                                                    <label class="toggle" data-on="SI" data-off="NO">
                                                                                        <input inputmode="none"  type="checkbox" id="inParParEstado2" name="checkbox-toggle" ng-true-value="1" ng-false-value="0" ng-model="cloneParametro.estado2">
                                                                                        <span class="button-checkbox"></span>
                                                                                    </label>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-lg-8 col-md-6"></div>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label class="col-lg-2 col-md-3 control-label" for="inParIntegracion"><b>Tabla Integración</b></label>
                                                                            <div class="col-lg-10 col-md-9">
                                                                                <select id="inParIntegracion" class="form-control" ng-model="cloneParametro.descripcionIntegracion" ng-change="cargarRegistrosTablaIntegracionColeccion()">
                                                                                    <option ng-repeat="tabla in tablasIntegracion" value="{{tabla.idTabla}}">{{tabla.tabla}}</option>
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label class="col-lg-2 col-md-3 control-label" for="inParIdIntegracion"><b>Registro Integración</b></label>
                                                                            <div class="col-lg-10 col-md-9">
                                                                                <select id="inParIdIntegracion" class="form-control" ng-model="cloneParametro.idIntegracion">
                                                                                    <option ng-repeat="fila in registrosIntegracion" value="{{fila.id}}">{{fila.descripcion}}</option>
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label class="col-lg-2 col-md-3 control-label"><b>Modificado</b></label>
                                                                            <div class="col-lg-3 col-md-3"><p class="form-control-static">{{cloneParametro.usuarioModifico}}</p></div>
                                                                            <div class="col-lg-3 col-md-3"><p class="form-control-static">{{cloneParametro.fechaModificado}}</p></div>
                                                                            <div class="col-lg-3 col-md-3"><p class="form-control-static">{{cloneParametro.horaModificado}}</p></div>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!-- End Formulario -->
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <center>
                                                        <button class="btn btn-default" ng-click="closeModalParametro()">Cancelar</button>
                                                        <button class="btn btn-primary" ng-click="changeParametro()">
                                                            <i class="glyphicon glyphicon-floppy-saved mr10"></i>Guardar
                                                        </button>
                                                    </center>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- End Modal Parametros -->
                                    </div>
                                    <!-- End Contenedor Parametros -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="mdl_rdn_pdd_crgnd" class="modal_cargando" ng-show="cargando">
            <div id="mdl_pcn_rdn_pdd_crgnd" class="modal_cargando_contenedor">
                <img src="../../imagenes/admin_resources/progressBar.gif"/>
            </div>
        </div>

        <script type="text/javascript" src="../../js/angular/angular.js"></script>
        <script type="text/javascript" src="../../js/angular/angular-resource.min.js"></script>
        <script type="text/javascript" src="../../js/ajax_politicas.js"></script>

    </body>
</html>