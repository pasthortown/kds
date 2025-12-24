<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Configuración Promoción</title>
    <!-- ESTILOS -->
    <link rel="stylesheet" href="../../bootstrap/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="../../bootstrap/css/switch.css"/>
    <link rel="stylesheet" href="../../bootstrap/templete/css/plugins.css"/>
    <link rel="stylesheet" href="../../css/chosen.css"/>
    <link rel="stylesheet" href="../../bootstrap/css/bootstrap-editable.css"/>
    <link rel="stylesheet" href="../../css/alertify.default.css"/>
    <link rel="stylesheet" href="../../css/select2.css">
    <link rel="stylesheet" href="../../css/list.css">
    <link rel="stylesheet" href="../../css/bootstrap-datetimepicker.css"/>
</head>
<body>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <!-- Nav tabs -->
            <ul class="nav nav-tabs">
                <li role="presentation" class="active"><a href="#PromocionGeneral" aria-controls="home" role="tab"
                                                          data-toggle="tab">General</a></li>
                <li role="presentation"><a href="#PromocionRestaurantes" aria-controls="profile" role="tab" data-toggle="tab">Configuraciones</a></li> 
            </ul>
            <!-- Tab panes -->
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="PromocionGeneral">
                    <div class="row">
                        <div class="col-md-12">
                            <span class="label label-primary">Datos generales</span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-horizontal">
                                        <div class="form-group"><br>
                                            <label for="nombrePromocion" class="col-sm-2" style="font-size: 12px;">Nombre</label>
                                            <div class="col-sm-10">
                                                <input inputmode="none"  class="form-control" id="nombrePromocion"
                                                       placeholder="Nombre Promoción">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-horizontal">
                                        <div class="form-group"><br>
                                            <label for="nombreImprimiblePromocion" class="col-sm-2 control-label"
                                                   style="font-size: 12px;">Imprimible</label>
                                            <div class="col-sm-10">
                                                <input inputmode="none"  class="form-control" id="nombreImprimiblePromocion"
                                                       placeholder="Nombre Imprimible">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-12"><span class="label label-primary">Fecha Validez</span></div>
                            </div>
                            <div class="row">
                                <div class="form-horizontal">
                                    <div class="col-sm-6">
                                        <div class="form-group"><br>
                                            <label for="inputEmail3" class="col-sm-2 control-label"
                                                   style="font-size: 12px;">Desde</label>
                                            <div class="col-sm-7">
                                                <div class='input-group date' id='datetimepicker6'>
                                                    <input inputmode="none"  type='text' class="form-control" placeholder="Desde"/>
                                                    <span class="input-group-addon">
                                               <span class="glyphicon glyphicon-calendar"></span>
                                           </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group"><br>
                                            <label for="inputEmail3" class="col-sm-2 control-label"
                                                   style="font-size: 12px;">Hasta</label>
                                            <div class="col-sm-7">
                                                <div class='input-group date' id='datetimepicker7'>
                                                    <input inputmode="none"  type='text' class="form-control" placeholder="Hasta"/>
                                                    <span class="input-group-addon">
                                               <span class="glyphicon glyphicon-calendar"></span>
                                           </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-12"><span class="label label-primary">Restricción Horarios</span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-horizontal">
                                    <div class="col-sm-6">
                                        <div class="form-group"><br>
                                            <label for="horarioDesde" class="col-sm-2 control-label"
                                                   style="font-size: 12px;">Desde</label>
                                            <div class="col-sm-7">
                                                <input inputmode="none"  class="form-control horario" id="horarioDesde"
                                                       placeholder="Desde">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group"><br>
                                            <label for="horarioHasta" class="col-sm-2 control-label"
                                                   style="font-size: 12px;">Hasta</label>
                                            <div class="col-sm-7">
                                                <input inputmode="none"  class="form-control horario" id="horarioHasta"
                                                       placeholder="Hasta">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-sm-6">
                            <span class="label label-primary">Restricción días 
                             </span>
                                    <br><br>
                                    <div class="switch"></div>
                                </div>
                                <div class="form-inline col-sm-12">
                                    <label class="customcheck">Lunes
                                        <input inputmode="none"  type="checkbox" checked="checked">
                                        <span class="checkmark"></span>
                                    </label>
                                    <label class="customcheck">Martes
                                        <input inputmode="none"  type="checkbox">
                                        <span class="checkmark"></span>
                                    </label>
                                    <label class="customcheck">Miércoles
                                        <input inputmode="none"  type="checkbox">
                                        <span class="checkmark"></span>
                                    </label>
                                    <label class="customcheck">Jueves
                                        <input inputmode="none"  type="checkbox">
                                        <span class="checkmark"></span>
                                    </label>
                                    <label class="customcheck">Viernes
                                        <input inputmode="none"  type="checkbox">
                                        <span class="checkmark"></span>
                                    </label>
                                    <label class="customcheck">Sábado
                                        <input inputmode="none"  type="checkbox">
                                        <span class="checkmark"></span>
                                    </label>
                                    <label class="customcheck">Domingo
                                        <input inputmode="none"  type="checkbox">
                                        <span class="checkmark"></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-12">
                                    <span class="label label-primary">Restricciones número de  canjes</span></div>
                            </div>
                            <div class="row">
                                <div class="form-horizontal">
                                    <div class="col-sm-12">
                                        <div class="form-group"><br>
                                            <label for="horarioDesde" class="col-sm-6 control-label"
                                                   style="font-size: 12px;">Max. Canjes</label>
                                            <div class="col-sm-6">
                                                <input inputmode="none"  class="form-control horario" id="horarioDesde"
                                                       placeholder="Máximo" onKeyPress="return justNumbers(event);">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="horarioHasta" class="col-sm-6 control-label"
                                                   style="font-size: 12px;">Max. Canjes Usuario</label>
                                            <div class="col-sm-6">
                                                <input inputmode="none"  class="form-control horario" id="horarioHasta"
                                                       placeholder="Máximo" onKeyPress="return justNumbers(event);">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-sm-6">
                            <span class="label label-primary">Restricción Productos
                            </span>
                                </div>
                                <br><br>
                                <div class="col-sm-12">
                                    <select class="js-example-basic-multiple col-sm-12" name="states[]"
                                            multiple="multiple" placeholder="Seleccione un producto">
                                        <option value="AL">BIFE DE CHORIZO (PURE DE PAPA) AFTER OFFICE PROMO</option>
                                        <option value="WY">MOUSSE DE CHOCOLATE ALMENDRO MOD</option>
                                        <option value="WYA">FILETE DE POLLO BBQ (PAPA FRITA) TARJETON R003</option>
                                        <option value="WY3">COMBO NAVIDENO P CHAUPI</option>
                                        <option value="WYAS">JUGO GUANABANA</option>
                                        <option value="WY09">PICANA (PURE DE PAPA)</option>
                                        <option value="WY09">CERVEZA MILLER DRAFT</option>
                                        <option value="WY09">AGRANDA COLA 22 oz</option>
                                        <option value="WY09">BONY 50 GR</option>
                                        <option value="WY09">LOMO FINO (PAPA CHAUPI) TARJETON R003</option>

                                    </select>

                                </div>


                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-12"><span class="label label-primary">Canal Compra Específico</span>
                                </div>
                            </div>
                            <div class="row">
                                <br>
                                <div class="col-sm-12">

                                    <div class="col-sm-12">

                                        <select class="js-example-basic-multiple col-sm-12" name="states[]"
                                                multiple="multiple" placeholder="Seleccione un canal de compra">
                                            <option value="A1">Salón</option>
                                            <option value="A2">Levar</option>

                                        </select>

                                    </div>


                                </div>

                            </div>
                        </div>
                    </div>

                    <hr>


                    <div class="row">
                        <div class="col-md-6">

                            <div class="col-sm-6">
                            <span class="label label-primary">Otras Restricciones
                               
                            </span>
                            </div>
                            <div class="col-sm-10">
                                <div class="col-md-6">Caduca con el tiempo</div>
                                <div class="col-md-6">
                                    <div class="form-group">

                                        <input inputmode="none"  type="email" class="form-control col-md-1" id="inputTiempoCaducidad"
                                               placeholder="tiempo" onKeyPress="return justNumbers(event);">

                                        <select id="inputMedidaTiempoCaducidad" class="form-control">
                                            <option value="horas">Horas</option>
                                            <option value="dias" selected>Días</option>
                                            <option value="semanas">Semanas</option>
                                            <option value="meses">Meses</option>
                                        </select>
                                    </div>

                                </div>


                                <div class="col-md-6">Restricción de edad</div>
                                <div class="col-md-6">
                                    <div class="form-group">

                                        <input inputmode="none"  type="email" class="form-control" id="inputMinimoEdad" placeholder="edad"
                                               onkeypress="return justNumbers(event);">

                                    </div>
                                </div>
                                <div class="col-md-6">Restricción Monto Factura</div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <input inputmode="none"  type="email" class="form-control" id="inputMinimoEdad"
                                               placeholder="Monto">
                                    </div>
                                </div>
                                <div class="col-md-6">Número de Productos</div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <input inputmode="none"  type="email" class="form-control" id="inputMinimoEdad"
                                               placeholder="Número de Productos"
                                               onkeypress="return justNumbers(event);">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="col-sm-6">
                                </div>
                                <div class="col-sm-10">
                                    <div class="col-md-6">Máximo Canje Múltiple</div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <input inputmode="none"  type="email" class="form-control" id="inputMinimoEdad"
                                                   placeholder="Número de canjes máximo"
                                                   onkeypress="return justNumbers(event);">
                                        </div>
                                    </div>
                                    <div class="col-md-6">Permite Otras promociones</div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div class="switch"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">Requiere Código Específico</div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div class="switch"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">Descuento sobre descuento</div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div class="switch"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                </div>
            </div>
        </div>
        <!-- LIBRERIAS -->
        <script type="text/javascript" src="../../js/jquery-1.11.3.min.js"></script>
        <script type="text/javascript" src="../../js/jquery-ui.js"></script>
        <script type="text/javascript" src="../../js/ajax_datatables.js"></script>
        <script type="text/javascript" src="../../bootstrap/js/bootstrap.js"></script>
        <script type="text/javascript" src="../../bootstrap/js/moment.js"></script>
        <script type="text/javascript" src="../../bootstrap/js/DanGrossmanDateRangePicker/daterangepicker.js"></script>
        <script type="text/javascript" src="../../bootstrap/js/bootstrap-dataTables.js"></script>
        <script type="text/javascript" src="../../bootstrap/js/bootstrap-editable.min.js"></script>
        <script type="text/javascript" src="../../bootstrap/js/switch.js"></script>
        <script type="text/javascript" src="../../js/jquery.treetable.js"></script>
        <script type="text/javascript" src="../../js/chosen.jquery.min.js"></script>
        <script type="text/javascript" src="../../js/chosen.proto.min.js"></script>
        <script type="text/javascript" src="../../js/alertify.js"></script>
        <script type="text/javascript" src="../../js/moment-2.22.2.js"></script>
        <script type="text/javascript" src="../../js/ajax_select2.js"></script>
        <!-- http://eonasdan.github.io/bootstrap-datetimepicker/ -->
        <script type="text/javascript" src="../../js/bootstrap-datetimepicker.js"></script>

        <script type="text/javascript">
            $(document).ready(function () {
                $('#horarioDesde').datetimepicker({
                    stepping: 30,
                    format: "HH:mm"
                });
                $('#horarioHasta').datetimepicker({
                    useCurrent: false, //Important! See issue #1075
                    stepping: 30,
                    format: "HH:mm"
                });
                $("#horarioDesde").on("dp.change", function (e) {
                    $('#horarioHasta').data("DateTimePicker").minDate(e.date);
                });
                $("#horarioHasta").on("dp.change", function (e) {
                    $('#horarioDesde').data("DateTimePicker").maxDate(e.date);
                });
            });
        </script>

        <script type="text/javascript">
            $(function () {
                $('#datetimepicker6').datetimepicker();
                $('#datetimepicker7').datetimepicker({
                    useCurrent: false //Important! See issue #1075
                });
                $("#datetimepicker6").on("dp.change", function (e) {
                    $('#datetimepicker7').data("DateTimePicker").minDate(e.date);
                });
                $("#datetimepicker7").on("dp.change", function (e) {
                    $('#datetimepicker6').data("DateTimePicker").maxDate(e.date);
                });
            });

            function justNumbers(e) {
                var keynum = window.event ? window.event.keyCode : e.which;
                if ((keynum == 8) || (keynum == 46))
                    return true;

                return /\d/.test(String.fromCharCode(keynum));
            }

            $(document).ready(function () {
                $('.switch').click(function () {
                    $(this).toggleClass("switchOn");
                });
                $('.js-example-basic-multiple').select2();
            });
        </script>


</body>
</html>
