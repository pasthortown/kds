<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////
////////DESARROLLADO POR: PIERRE QUITIAQUEz///////////////////////////////////////////////////////////////
///////////DESCRIPCION: PLUS/PRECIOS  //////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////


include_once"../../system/conexion/clase_sql.php";
include_once"../../clases/clase_seguridades.php";
include_once"../../seguridades/seguridad.inc";
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="content-type" content="text/html; charset=ISO-8859-1" />
    <title>Replica Inicial</title>
    <!---------------------------------------------------
                           ESTILOS
    ----------------------------------------------------->
    <link rel="stylesheet" href="../../css/jquery-ui.css" type="text/css"/>
    <link rel="stylesheet" type="text/css" href="../../css/est_adminPantallaReplica.css" />
    <link rel="stylesheet" type="text/css" href="../../css/alertify.core.css"/>
    <link rel="stylesheet" type="text/css" href="../../css/alertify.default.css"/>
    <link rel="stylesheet" type="text/css" href="../../bootstrap/css/bootstrap.css"/>
    <!--<link rel="stylesheet" type="text/css" href="../../bootstrap/css/select2.css" />-->
    <!--<link rel="stylesheet" type="text/css" href="../../css/select2.css" />-->
    <link rel="stylesheet" type="text/css" href="../../css/chosen.css" />
    <link media="all" href="../../css/progressBar.css" rel="stylesheet" />
    <!---------------------------------------------------
                       JSQUERY
    ----------------------------------------------------->
    <script src="../../js/jquery1.11.1.js"></script>
    <script language="javascript1.1" type="text/javascript" src="../../js/ajax_datatables.js"></script>
    <!--<script src="../../js/ajax_select2.js"></script>-->
    <script language="javascript1.1" type="text/javascript" src="../../bootstrap/js/bootstrap.js"></script>
    <script language="javascript1.1" type="text/javascript" src="../../bootstrap/js/bootstrap-dataTables.js"></script>
    <script language="javascript1.1"  src="../../js/alertify.js"></script>
    <script type="text/javascript" src="../../js/ajax_adminReplicaInicial.js"></script>
    <script language="javascript1.1" type="text/javascript" src="../../js/js_validaciones.js"></script>

    <script language="javascript1.1" type="text/javascript" src="../../js/chosen.jquery.js"></script>
    <script language="javascript1.1" type="text/javascript" src="../../js/chosen.jquery.min.js"></script>
    <script language="javascript1.1" type="text/javascript" src="../../js/chosen.proto.js"></script>
    <script language="javascript1.1" type="text/javascript" src="../../js/chosen.proto.min.js"></script>


</head>

<body>
<div class="superior">
    <div class="tituloPantalla">
        <h1>ADMINISTRACI&Oacute;N DE R&Eacute;PLICA INICIAL</h1>
    </div>
</div>
<br>
<br>
<br>
<br>
<div class="container"></div>,<div class="container">

    <div class="stepwizard col-md-offset-3">
        <div class="stepwizard-row setup-panel">
            <div class="stepwizard-step">
                <a href="#step-1" type="button" class="btn btn-primary"><span class="glyphicon glyphicon-transfer"></span></a>
                <p>Conexi&oacute;n</p>
            </div>
            <div class="stepwizard-step">
                <a href="#step-2" type="button" class="btn btn-default" disabled="disabled"><span class="glyphicon glyphicon-hdd"></a>
                <p>Instalaci&oacute;n I</p>
            </div>
            <div class="stepwizard-step">
                <a href="#step-3" type="button" class="btn btn-default" disabled="disabled"><span class="glyphicon glyphicon-hdd"></a>
                <p>Instalaci&oacute;n II</p>
            </div>
            <div class="stepwizard-step">
                <a href="#step-4" type="button" class="btn btn-default" disabled="disabled"><span class="glyphicon glyphicon-home"></a>
                <p>Local</p>
            </div>
            <div class="stepwizard-step">
                <a href="#step-5" type="button" class="btn btn-default" disabled="disabled"><span class="glyphicon glyphicon-download-alt"></a>
                <p> Replicaci&oacute;n</p>
            </div>
            <div class="stepwizard-step">
                <a href="#step-6" type="button" class="btn btn-default" disabled="disabled"><span class="glyphicon glyphicon-log-out"></a>
                <p>Finalizar</p>
            </div>
        </div>
    </div>

    <div class="progress" style="display: none" id="progresar">
        <div id="dynamic"  class="progress-bar progress-bar-success progress-bar-striped active" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
            <span id="current-progress"></span>
        </div>
    </div>
    <div class="row setup-content table-bordered " id="step-1">
        <div class="col-xs-6 col-md-offset-3">
            <div class="col-md-12">
                <h3>Configuraci&oacute;n de Conexi&oacute;n </h3>
                <div class="form-group">
                    <span class="glyphicon glyphicon-cloud"></span>
                    <label class="control-label">Servidor \ Instancia</label>
                    <input inputmode="none"  maxlength="100" type="text" class="form-control" value="" required="required"
                           id="server"
                           placeholder="Ingrese el Servidor \ Instancia"/>
                </div>
                <div class="form-group">
                    <span class="glyphicon glyphicon-user"></span>
                    <label class="control-label">Usuario</label>
                    <input inputmode="none"  maxlength="100" type="text" value="" class="form-control" required="required"
                           id="usr"
                           placeholder="Ingrese el Usuario de la Base de Datos"/>
                </div>
                <div class="form-group">
                    <span class="glyphicon glyphicon-lock"></span>
                    <label class="control-label">Contraseña</label>
                    <input inputmode="none"  type="password" class="form-control" value="" required="required" id="contrasena"
                           placeholder="Ingrese la Contraseña"/>
                </div>
                <span class="glyphicon glyphicon-wrench"></span>
                <button class="btn btn-primary" type="button" id="test" onclick="conexion();">Test Connection</button>
                <span style="margin-left: 30px;"></span>
                <span class="glyphicon  glyphicon-random"></span>
                <button class="btn btn-alt"  type="button" id="linkedservers" onclick="ejecutarScriptsLinked()" >Linked-Servers</button>
                <br>
                <br>
                <button class="btn btn-primary nextBtn btn-lg pull-right ml5 mb10" id="avanzar0" onclick="continuarInicio();" type="button">Siguiente</button>
            </div>
        </div>
    </div>
    <div class="row setup-content table-bordered" id="step-2">
        <div class="col-xs-6 col-md-offset-3">
            <h3>Instalaci&oacute;n Inicial I</h3>
            <h4 align="center"><b>Version</b></h4>
            <div class="form-group ">
                <label class="control-label ">Ruta de Instalaci&oacute;n de archivos de datos de
                    DB_Versi&oacute;n</label>
                <input inputmode="none"  id="data1"  value="B:\BD\DB_Versions\" maxlength="100" required="required" type="text" class="form-control" placeholder="Ingrese el ruta de la Version"/>
            </div>
            <div class="form-group ">
                <label class="control-label ">Ruta de Instalaci&oacute;n de de archivos de log de
                    DB_Versi&oacute;n</label>
                <input inputmode="none"  id="log1"  value="L:\BD\DB_Versions\" maxlength="100" required="required" type="text" class="form-control" placeholder="Ingrese el ruta de MaxpointLog"/>
            </div>
            <div  align="center">
                <input inputmode="none"  id="btinstalarVersions"  class="btn btn-success" value="Instalar"  type="button" onclick="validarCampos();"  />
            </div>
            <br>
            <div class="row">
                <table class="table table-bordered table-hover" id="Instalacion" border="1" cellpadding="1" cellspacing="0">
                    <thead>
                    <tr class='active'>
                        <th style='text-align:center'>Proceso</th>
                        <th style='text-align:center' >Ejecuci&oacute;n</th>
                        <th style='text-align:center'>Resultado</th>
                        <th style='text-align:center'>Log</th>
                    </tr>
                    </thead>
                    <tr>
                        <td style='text-align: center; width:300px'>Creaci&oacute;n de Trigger capturador de eventos
                        </td>
                        <td  style='text-align: center; width:200px'></td>
                        <td style='text-align: center; width:200px'></td>
                        <td style='text-align: center; width:200px'></td>
                    </tr>
                    <tr>
                        <td style='text-align: center; width:300px'>Instalaci&oacute;n de Base de datos DB_Versions</td>
                        <td style='text-align: center; width:200px'></td>
                        <td style='text-align: center; width:200px'></td>
                        <td style='text-align: center; width:200px'></td>
                    </tr>
                </table>

                <button class="btn btn-primary prevBtn btn-lg pull-left" id="atras1" onclick="progreso(1);" type="button">Atras</button>
                <button class="btn btn-primary nextBtn btn-lg pull-right" id="avanzar1" onclick="progreso(0);" type="button">Siguiente</button>
            </div>
        </div>
    </div>
    <div class="row setup-content table-bordered" id="step-3">
        <div class="col-xs-6 col-md-offset-3">
            <h3>Instalaci&oacute;n Inicial II </h3>
            <h4 align="center"><b>Maxpoint Log</b></h4>
            <div class="form-group ">
                <label class="control-label ">Ruta de Instalaci&oacute;n de archivos de datos de Maxpoint-Log</label>
                <input inputmode="none"  id="data2"  value="" maxlength="100" required="required" type="text" class="form-control"  placeholder="Ingrese el ruta de MaxpointLog"/>
            </div>
            <div class="form-group ">
                <label class="control-label ">Ruta de Instalaci&oacute;n de archivos de Log de Maxpoint-Log</label>
                <input inputmode="none"  id="log2"  value="" maxlength="100" required="required" type="text" class="form-control" placeholder="Ingrese el ruta de MaxpointLog"/>
            </div>
            <div  align="center">
                <input inputmode="none"  id="btinstalarLog"  class="btn btn-success" value="Instalar"  type="button" onclick="validarCamposMaxp();"  />
            </div>
            <br>
            <div class="row">
                <table class="table table-bordered table-hover" id="Fase" border="1" cellpadding="1" cellspacing="0">
                    <thead>
                    <tr class='active'>
                        <th style='text-align:center'>Proceso</th>
                        <th style='text-align:center' >Ejecuci&oacute;n</th>
                        <th style='text-align:center'>Resultado</th>
                        <th style='text-align:center'>Log</th>
                    </tr>
                    </thead>
                    <tr>
                        <td style='text-align: center; width:300px'>Creaci&oacute;n de Base de datos MaxpointLog</td>
                        <td  style='text-align: center; width:200px'></td>
                        <td style='text-align: center; width:200px'></td>
                        <td style='text-align: center; width:200px'></td>
                    </tr>
                </table>

                <button class="btn btn-primary prevBtn btn-lg pull-left" id="atras3" onclick="progreso(1)" type="button">Atras</button>
                <button class="btn btn-primary nextBtn btn-lg pull-right" id="avanzar3" onclick="progreso(0);" type="button">Siguiente</button>
            </div>
        </div>
    </div>
    <div class="row setup-content table-bordered" id="step-4">
        <div class="col-xs-6 col-md-offset-3">
            <div class="col-md-12">
                <h3> Replicaci&oacute;n de Local</h3>
                <div class="form-group ">
                    <label class="control-label ">Seleccione Cadena:</label>
                        <tr>
                            <td>
                                <select id="cadena4" class="form-control" required="required" ></select>
                            </td>
                        </tr>
                    </div>

                <div class="form-group ">
                    <label class="control-label ">Seleccione Restaurante:</label>

                        <tr>
                            <td>
                                <select id="tienda4" class="form-control" required="required" ></select>
                            </td>
                        </tr>

                </div>
                <div class="form-group ">
                    <label class="control-label ">Nombre Base de Datos</label>
                    <input inputmode="none"  id="base4" value="" maxlength="30" required="required" type="text" class="form-control"
                           placeholder="Ingrese el nombre de la base de datos"/>
                </div>
                <div class="form-group ">
                    <label class="control-label ">Ruta de Instalaci&oacute;n de archivos de Datos de Base de
                        datos</label>
                    <input inputmode="none"  id="data4"  value="" maxlength="100" required="required" type="text" class="form-control" placeholder="Ingrese el ruta de instalacion de la data"/>
                </div>
                <div class="form-group ">
                    <label class="control-label ">Ruta de Instalaci&oacute;n de archivos de Log de Base de datos</label>
                    <input inputmode="none"  id="log4"  value="" maxlength="100" required="required" type="text" class="form-control" placeholder="Ingrese el ruta de instalacion de historial"/>
                </div>

                <button class="btn btn-primary prevBtn btn-lg pull-left" id="atras2" onclick="progreso(1)" type="button">Atras</button>
                <button class="btn btn-primary nextBtn btn-lg pull-right" id="avanzar2" onclick="continuaReplica()" type="button">Siguiente</button>
            </div>
        </div>
    </div>
    <div class="row setup-content table-bordered" id="step-5">
        <div class="col-xs-6 col-md-offset-3">
            <div class="col-md-12">
                <h3>Replicaci&oacute;n</h3>
                <table class="table table-bordered table-hover" id="ejecucion" border="1" cellpadding="1" cellspacing="0">
                    <thead><tr class='active'>
                        <th style='text-align:center'>Proceso</th>
                        <th style='text-align:center' >Ejecuci&oacute;n</th>
                        <th style='text-align:center'>Resultado</th>
                        <th style='text-align:center'>Log</th>
                    </tr></thead>
                    <tr data-toggle="collapse" data-target="#accordion" class="clickable" onclick="abrirOculto()" >
                        <td style='text-align: center; width:300px'>Creaci&oacute;n Estructura de Base</td>
                        <td style='text-align: center; width:200px'></td>
                        <td style='text-align: center; width:200px'></td>
                        <td style='text-align: center; width:200px'></td>
                    </tr>
                    <tr id="mostrar" style="display:none;">
                        <td colspan="4">
                            <div id="accordion" class="collapse" >
                                <div><b>Observaciones:</b></div>
                                <div id="a" style="margin-left: 50px;"></div>
                                <div id="b" style="margin-left: 50px;"></div>
                                <div id="c" style="margin-left: 50px;"></div>
                                <div id="d" style="margin-left: 50px;"></div>
                                <div id="e" style="margin-left: 50px;"></div>
                                <div id="f" style="margin-left: 50px;"></div>
                            </div>
                        </td>
                    </tr>
                    <tr><td style='text-align: center; width:300px'>Replicaci&oacute;n Inicial</td>
                        <td style='text-align: center; width:200px'></td>
                        <td style='text-align: center; width:200px'></td>
                        <td style='text-align: center; width:200px'></td></tr>

                    <tr><td style='text-align: center; width:300px'>Verificaci&oacute;n e Inserci&oacute;n de reportes</td>
                        <td style='text-align: center; width:200px'></td>
                        <td style='text-align: center; width:200px'></td>
                        <td style='text-align: center; width:200px'></td></tr>

                    <tr><td style='text-align: center; width:300px'>Creaci&oacute;n de Periodo Inicial</td>
                        <td style='text-align: center; width:200px'></td>
                        <td style='text-align: center; width:200px'></td>
                        <td style='text-align: center; width:200px'></td></tr>

                    <tr><td style='text-align: center; width:300px'>Creaci&oacute;n de Usuario Reportes</td>
                        <td style='text-align: center; width:200px'></td>
                        <td style='text-align: center; width:200px'></td>
                        <td style='text-align: center; width:200px'></td></tr>

                    <tr><td style='text-align: center; width:300px'>Permisos en Reportes</td>
                        <td style='text-align: center; width:200px'></td>
                        <td style='text-align: center; width:200px'></td>
                        <td style='text-align: center; width:200px'></td></tr>

                    <tr><td style='text-align: center; width:300px'>Creaci&oacute;n - Control de Estaci&oacute;n</td>
                        <td style='text-align: center; width:200px'></td>
                        <td style='text-align: center; width:200px'></td>
                        <td style='text-align: center; width:200px'></td></tr>

                </table>
                <button class="btn btn-primary  prevBtn btn-lg pull-left" style='display:none' id="regresa"
                        type="button">Atras
                </button>
                <button class="btn btn-primary nextBtn btn-lg pull-right" onclick="progreso(0);borrarArchivosReplica();"
                        type="button">Siguiente
                </button>
            </div>
        </div>
    </div>
    <div class="row setup-content table-bordered" id="step-6">
        <div class="col-xs-6 col-md-offset-3">
            <h3>Instalar JSON-Select</h3>
            <br>
            <div class="row" >
                <table class="table table-bordered table-hover" id="JSON" border="1" cellpadding="1" cellspacing="0">
                    <thead><tr class='active'>
                        <th style='text-align:center'>Servidor</th>
                        <th style='text-align:center'>Base de datos</th>
                        <th style='text-align:center'>Usuario</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr><td align="center"> </td>
                        <td align="center" ></td>
                        <td align="center"></td>
                    <tr>
                    </tbody>
                </table>
                <br>
                <table class="table table-bordered table-hover"  border="1" cellpadding="1" cellspacing="0">
                    <thead><tr class='active'>
                        <th style='text-align:center'>Instalar Json Select</th>
                    </thead>
                    <tbody>
                    </tr>
                    <tr><td align="center">
                            <a href="../../scripts/Programa.exe"><button class="btn btn-warning" onclick="borrarTArchivos()" type="button" id="json">
                                    <span class="glyphicon glyphicon-floppy-save"></span> JSON Select</button></a>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <br>
                <br>
                <br><br>
                <button class="btn btn-primary prevBtn btn-lg pull-left" onclick="progreso(1)" type="button">Atras</button>
                <button class="btn btn-success btn-lg pull-right" type="submit" onclick="verificarJson();">Finalizar</button>

            </div>
        </div>
    </div>


</div>

<div id="cargandoi" style="display:none" class="modal_cargando">
    <div id="cargandoi" class="modal_cargando_contenedor">
        <img src="../../imagenes/admin_resources/progressBar.gif"/>
    </div>
</div>

<div id="timer1"  style="display:none" class="timer">
    <div id="timer" class="timer_c">
        <canvas id="mycanvas"  width="220" height="220"></canvas>
    </div>
</div>

<div id="ejecutalinkeds" style="display:none" class="timer">
    <div class="loader">
        <div class="bar1"></div>
        <div class="bar2"></div>
        <div class="bar3"></div>
        <div class="bar4"></div>
        <div class="bar5"></div>
        <div class="bar6"></div>
    </div>
</div>


</body>

