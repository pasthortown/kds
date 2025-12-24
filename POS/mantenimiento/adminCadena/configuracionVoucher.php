<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="content-type" content="text/html; charset=ISO-8859-1" />
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

        <!-- ESTILOS -->
        <link rel="stylesheet" type="text/css" href="../../bootstrap/css/bootstrap.min.css" />
        <link rel="stylesheet" type="text/css" href="../../bootstrap/css/bootstrap-editable.css" />
        <link rel='stylesheet' type ='text/css' href="../../css/style_configuracionVoucher.css" />
        <link rel="stylesheet" type="text/css" href="../../bootstrap/css/switch.css" />

    </head>
    <body>
        <div class="superior">
            <div class="menu" align="center" style="width: 300px;">
                <ul>
                    <li>

                        <button id="agregar" data-toggle="modal" data-target="#modal" class="botonMnSpr l-basic-elaboration-document-plus" onclick="fn_agregarVoucher()">
                        </button>
                    </li>
                </ul>
            </div>
            <div class="tituloPantalla">
                <h1>CONFIGURACION VOUCHER</h1>
            </div>
        </div>
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <h3>Configuración Voucher</h3>
                    <div class="tabbable" id="tabs-419968">
                        <ul class="nav nav-tabs">
                            <li class="active">
                                <a href="#panel-54983" data-toggle="tab">Voucher Aerolineas</a>
                            </li>
<!--                            <li>
                                <a href="#panel-553348" data-toggle="tab">Cupones Externos</a>
                            </li>-->
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane active" id="panel-54983">
                                <br/>
                                <table id="listadoVoucher" class="table table-striped table-hover" cellspacing="0" width="100%">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th>Nombre</th>
                                            <th>Ruc</th>
                                            <th>Dirección</th>
                                            <th>Correo Electrónico</th>
                                            <th>Teléfono</th>
                                            <th>Activo</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        
                                    </tbody>
                                </table>
                            </div>
                            <div class="tab-pane" id="panel-553348">
                                <p> Tabla </p>
                            </div>
                        </div>
                    </div>


                    <div class="modal fade" id="modal-container-129396" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">

                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                                        ×
                                    </button>
                                    <h4 class="modal-title" id="myModalLabel">
                                        Buscar cliente externo y relacionado
                                    </h4>
                                </div>
                                <div class="modal-body">
                                    <div class="container-fluid">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="alert alert-success alert-dismissable hidden">
                                                    
                                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                                                        ×
                                                    </button>
                                                    <h4>
                                                        Alert!
                                                    </h4> <strong>Warning!</strong> Best check yo self, you're not looking too good. <a href="#" class="alert-link">alert link</a>
                                                </div>
                                                <form role="form" id="form_guardarVoucher">
                                                    <div id="activarPolitica"></div>
                                                    <div class="form-group">
                                                        <label class="radio-inline">Externo </label> <input inputmode="none"  type="radio" name="optradio" value="EXTERNO" checked/> 
                                                        <label class="radio-inline">Relacionado </label> <input inputmode="none"  type="radio" name="optradio" value="RELACIONADO" /> 
                                                    </div>
                                                    <div id="searchNombre" class="form-group">
                                                        <label for="fclienteExt">
                                                            Nombre cliente externo
                                                        </label>
                                                        <input inputmode="none"  id="search-box" type="text" class="form-control" list='search' oninput='onInput()' required/>
                                                        <datalist id='search'>
                                                            
                                                        </datalist>
                                                    </div>
                                                    <div class="form-group row">
                                                        <div class="col-xs-6">
                                                            <label>
                                                                Identificación
                                                            </label>
                                                            <input inputmode="none"  type="text" class="form-control" id="identificacionExt" disabled required/>
                                                        </div>
                                                        <div class="col-xs-6">
                                                            <label>
                                                                Teléfono
                                                            </label>
                                                            <input inputmode="none"  type="text" class="form-control" id="telefonoExt" disabled />
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>
                                                            Dirección
                                                        </label>
                                                        <input inputmode="none"  type="text" class="form-control" id="direccionExt" disabled required/>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>
                                                            Correo electrónico
                                                        </label>
                                                        <input inputmode="none"  type="text" class="form-control" id="correoExt" disabled />
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">
                                        Cerrar
                                    </button> 
                                    <button id ="btn_guardarCambios" type="submit" class="btn btn-primary" >
                                        Guardar Cambios
                                    </button>
                                    
                                </div>
                            </div>
                        </div>
                    </div>



                    <div class="modal fade" id="modal-container-monto" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">

                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                                        ×
                                    </button>
                                    <h4 class="modal-title" id="myModalLabel">
                                        Monto Voucher
                                    </h4>
                                </div>
                                <div class="modal-body">
                                    <div class="container-fluid">
                                        <div class="row">
                                            <div class="col-md-12">

                                                <form role="form">

                                                    <div class="form-group row">
                                                        <div class="col-xs-6">
                                                            <label>Monto Voucher 15 USD</label>
                                                            <div id="1" class="make-switch switch-small" style="height: 40px">
                                                                <input inputmode="none"  type="checkbox" name="my-checkbox"/>
                                                            </div>
                                                        </div>

                                                        <div class="col-xs-6">
                                                            <label>Monto Voucher 10 USD</label>
                                                            <div id="2" class="make-switch switch-small" style="height: 40px">
                                                                <input inputmode="none"  type="checkbox" name="my-checkbox" />
                                                            </div>
                                                        </div>

                                                    </div>
                                                    <div class='form-group row'>
                                                        <div class="col-xs-6">
                                                            <label>Monto Voucher 15 USD</label>
                                                            <div id="3" class="make-switch switch-small" style="height: 40px">
                                                                <input inputmode="none"  type="checkbox" name="my-checkbox" />
                                                            </div>
                                                        </div>

                                                        <div class="col-xs-6">
                                                            <label>Monto Voucher 10 USD</label>
                                                            <div id="4" class="make-switch switch-small" style="height: 40px">
                                                                <input inputmode="none"  type="checkbox" name="my-checkbox" />
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <div class='col-xs-3'>
                                                            <label>Otro valor</label>
                                                            <input inputmode="none"  type="text" class="form-control" id="valorMonto"/>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">

                                    <button type="button" class="btn btn-default" data-dismiss="modal">
                                        Cerrar
                                    </button> 
                                    <button type="button" class="btn btn-primary">
                                        Guardar cambios
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- JavaScript -->
        <script type="text/javascript" src="../../js/jquery-3.2.1.min.js"></script>
        <script type="text/javascript" src="../../js/ajax_datatables.js"></script>
        <script type="text/javascript" src="../../bootstrap/js/bootstrap.js"></script>
        <script type="text/javascript" src="../../bootstrap/js/bootstrap-dataTables.js"></script>
        <script type="text/javascript" src='../../js/ajax_configuracionVoucher.js'></script>
        <script type="text/javascript" src='../../bootstrap/js/switch.js'></script>

    </body>
</html>