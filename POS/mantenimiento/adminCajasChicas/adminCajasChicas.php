<?php
session_start();
////////////////////////////////////////////////////////////////////////////////////
////////DESARROLLADO POR: ANDRES ROMERO/////////////////////////////////////////////
///////////DESCRIPCION: DES-RELACIONAR CAJAS CHICAS ////////////////////////////////
////////////////API: servicios web sir /////////////////////////////////////////////
////////FECHA CREACION: 03/02/2022//////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////

include_once "../../system/conexion/clase_sql.php";
include_once "../../clases/clase_menu.php";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta http-equiv="content-type" content="text/html; charset=ISO-8859-1" />
        <title>Desrelacionar Cajas Chicas</title>

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
                <h1>DESRELACIONAR CAJAS CHICAS</h1>
            </div>
        </div>
        <br/>
        <div class="container">
            <div class="col-12">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Desde</th>
                            <th>Hasta</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <div class="input-prepend input-group">
                                    <span class="add-on input-group-addon"><i class="glyphicon glyphicon-calendar fa fa-calendar"></i></span>
                                    <input inputmode="none"  type="text" id="fecha_inicio" class="form-control" placeholder="Fecha Inicio" />
                                </div>
                            </td>
                            <td>
                                <div class="input-prepend input-group">
                                    <span class="add-on input-group-addon"><i class="glyphicon glyphicon-calendar fa fa-calendar"></i></span>
                                    <input inputmode="none"  type="text" id="fecha_fin" class="form-control" placeholder="Fecha Fin" />
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-12">
                <button type="button" class="btn btn-primary" onclick="buscar_cajasChicas()"> <i class="glyphicon glyphicon-search fa fa-search"></i> Buscar</button>
            </div>
            <div class="col-12" id="contenedor_cajasChicas" style="display: none">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Desde fecha</th>
                            <th>Hasta fecha</th>
                            <th>Valor total</th>
                        </tr>
                    </thead>
                    <tbody id="lista_cajasChicas">
                        <tr>
                            <td colspan="2">No se encontraron registros</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        
    <!-- Modal Begins -->
    <div class="modal fade" id="ModalCajasChicas" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-lg ">
            <div class="modal-content">
                <div class="modal-header panel-footer">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="tituloModal"></h4>
                </div>
                <br/>
                <div class="modal-body" id="cuerpoModal">
                    
                </div>
                <div>
                    <h5 class="modal-title" style="color: #000"><label>&nbsp;&nbsp;&nbsp;&nbsp; <span id="notaModal"></span></label></h5>
                </div>
                <div class="modal-footer panel-footer" >
                    <div align="center">
                        <div class="row" id="botonesModal"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Begins -->
        
        
        
        
        <!------- ARCHIVOS JS------------>
        
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
        <script src="../../js/ajax_adminCajasChicas.js"></script>
    </body> 
</html>