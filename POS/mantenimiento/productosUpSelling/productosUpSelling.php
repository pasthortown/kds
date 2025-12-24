<?php

/*
FECHA CREACION   : 05/02/2019 
DESARROLLADO POR : Daniel Llerena
DESCRIPCION      : Configuracion de productos Up Selling
*/

session_start();
?>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta charset="UTF-8" />
        <title>Productos Up Selling</title>
        <link rel="stylesheet" type="text/css" href="../../bootstrap/css/bootstrap.min.css" />        
        <link rel="stylesheet" type="text/css" href="../../css/chosen.css" />
    </head>
    <body>
        
        <input inputmode="none"  id="sessionIDCadena" type="hidden" value="<?php echo $_SESSION['cadenaId']; ?>"/>
        
        <div class="superior">     
            <div class="tituloPantalla">
                <h1>Productos Up Selling</h1>
            </div>   
        </div>
        <div class="contenedor">
            <br>        
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">
                        <button type="button" class="btn btn-primary" onclick="agregar();">
                            <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Agregar
                        </button>
                    </h3>
                </div>
                <div class="panel-body">
                    <div id="tabla_productos">
                        <table class="table table-bordered table-hover" id="tabla_detalleProductos" border="1" cellpadding="1" cellspacing="0"></table>
                    </div>
                </div>
            </div>              
        </div>
        
        <!-- Modal -->
        <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static">
            <div class="modal-dialog" role="document" style="width:900px;">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="cerrarModal(1);"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">Agregar Productos Up Selling</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-xs-3 text-right"><h5>Producto Base:</h5></div>
                            <div class="col-xs-9">
                                <div class="form-group">
                                    <select id="selectProductos1"></select>
                                </div>
                            </div>
                        </div>                        
                        <div class="row">  
                            <div class="col-xs-3 text-right"><h5>Producto Mejora:</h5></div>
                            <div class="col-xs-7">
                                <div class="form-group">
                                    <select id="selectProductos2" data-placeholder="Seleccionar Mejora de Producto Base" multiple="multiple" class="chosen-select"></select>
                                </div>
                            </div>
                            <button type="button" class="btn btn-primary" onclick="agregarProducto(1);">Agregar</button>
                        </div>
                        <div id="areaTrabajo">
                            <div class="col-md-12">
                                <div style="height:300px; overflow:auto;">
                                    <br/>
                                    <br/>
                                    <table id="tablaProductosMejora" class="table table-bordered"></table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal" onclick="cerrarModal(1);">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="modal fade" id="modalUpdate" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static">
            <div class="modal-dialog" role="document" style="width:900px;">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="cerrarModal(2);"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">Producto Up Selling : <label id="tituloProducto"></label></h4>
                    </div>
                    <div class="modal-body">
                        <div class="col-xs-12 text-right"><h6>Est&aacute; Activo?: <input inputmode="none"  type="checkbox" id="check_isactive" onchange="estadoProductoUpSelling(this.checked);"/></h6></div>                    
                        <br>

                        <div class="row">
                            <div class="col-xs-3 text-right"><h5>Producto Base:</h5></div>
                            <div class="col-xs-7">
                                <div class="form-group">
                                    <input inputmode="none"  type="text" class="form-control" id="txtProductoBase" style="text-transform:uppercase;" disabled="disabled"/>
                                </div>
                            </div>
                        </div>                        
                        <div class="row" id="divMejoraProducto">  
                            <div class="col-xs-3 text-right"><h5>Producto Mejora:</h5></div>
                            <div class="col-xs-7">
                                <div class="form-group">
                                    <select id="selectProductos2Update" data-placeholder="Seleccionar Mejora de Producto Base" multiple="multiple" class="chosen-select"></select>
                                </div>
                            </div>
                            <button type="button" class="btn btn-primary" onclick="agregarProducto(2);">Agregar</button>
                        </div>
                        <div id="areaTrabajo">
                            <div class="col-md-12">
                                <div style="height:300px; overflow:auto;">
                                    <br/>
                                    <br/>
                                    <table id="tablaProductosMejoraUpdate" class="table table-bordered"></table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal" onclick="cerrarModal(2);">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
        
        <input inputmode="none"  type="hidden" id="idProductoBase">
        
        <script type="text/javascript" src="../../js/jquery-1.11.3.min.js"></script>
        <script type="text/javascript" src="../../bootstrap/js/bootstrap.js"></script>
        <script type="text/javascript" src="../../js/ajax_datatables.js"></script>
        <script type="text/javascript" src="../../bootstrap/js/bootstrap-dataTables.js"></script>
        <script type="text/javascript" src="../../js/chosen.jquery.min.js"></script>
        <script type="text/javascript" src="../../js/chosen.proto.min.js"></script>
        <script type="text/javascript" src="../../js/ajax_productosUpSelling.js"></script>
        
    </body>
</html>

